<?php
namespace Tests\ServeurTests\Reponse;

use AlaroxRestServeur\Serveur\Reponse\ReponseManager;
use Tests\MockArg;
use Tests\TestCase;

class ReponseManagerTest extends TestCase
{
    /**
     * @var ReponseManager $restRequete
     */
    private $_restReponse;

    protected function setUp()
    {
        $this->_restReponse = new ReponseManager();
    }

    public function testRestSetHeader()
    {
        $header = $this->getMockHeaders();

        $this->_restReponse->setHeader($header);
        $this->assertAttributeEquals($header, '_header', $this->_restReponse);
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetHeaderInvalide()
    {
        $this->_restReponse->setHeader(null);
    }

    public function testRestSetFormatAcceptes()
    {
        $this->_restReponse->setFormatsAcceptes(array('JSON' => 'json', 'TEXT' => 'txt'));
        $this->assertCount(2, $this->_restReponse->getFormatsAcceptes());
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetFormatAcceptesInvalid()
    {
        $this->_restReponse->setFormatsAcceptes('ERROR');
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40000
     */
    public function testRestSetFormatAcceptesVide()
    {
        $this->_restReponse->setFormatsAcceptes(array());
    }

    public function testRestSetCharset()
    {
        $this->_restReponse->setCharset('utf-8');
        $this->assertEquals('utf-8', $this->_restReponse->getCharset());
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetCharsetInvalide()
    {
        $this->_restReponse->setCharset(9);
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40001
     */
    public function testRestSetCharsetInvalid()
    {
        $this->_restReponse->setCharset('UTF-9999999999');
    }

    public function testRestSetConfig()
    {
        $config = $this->createMock(
            'Config', new MockArg('getConfigValeur', array('JSON' => 'json', 'HTML' => 'html'), array('render')),
            new MockArg('getConfigValeur', 'utf-8', array('config.charset'))
        );

        $this->_restReponse->setConfig($config);
        $this->assertEquals('utf-8', $this->_restReponse->getCharset());
        $this->assertCount(2, $this->_restReponse->getFormatsAcceptes());
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetConfigInvalide()
    {
        $this->_restReponse->setConfig(null);
    }

    public function testSetFactoryRender()
    {
        $callable = function () {
        };

        $this->_restReponse->setRenderFactory($callable);
        $this->assertAttributeEquals($callable, '_renderFactory', $this->_restReponse);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testSetFactoryOnlyCallable()
    {
        $this->_restReponse->setRenderFactory(array());
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestRenderBug()
    {
        $this->_restReponse->fabriquerReponse($this->getMockObjetReponse(), 'boum');
    }

    public function testRestRenderAbstract()
    {
        $renderFactory = function () {
            $abstractrender = $this->createMock(
                'AbstractRenderer',
                new MockArg('genererRendu', '{"getKey":"getVar"}', array(array('getKey' => 'getVar')))
            );

            return $abstractrender;
        };

        $headerManager = $this->getMockHeaders();

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('getStatusHttp', 200),
            new MockArg('getDonneesReponse', array('getKey' => 'getVar'))
        );

        $this->_restReponse->setHeader($headerManager);
        $this->_restReponse->setRenderFactory($renderFactory);
        $this->_restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $this->_restReponse->fabriquerReponse($objetReponse, array('json'));

        $this->assertEquals('{"getKey":"getVar"}', $this->_restReponse->getContenuReponse());
    }

    public function testRestRenderNonTrouveRenvoi406()
    {
        $headerManager = $this->getMockHeaders();

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('setErreurHttp', 406)
        );

        $this->_restReponse->setHeader($headerManager);
        $this->_restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $this->_restReponse->fabriquerReponse($objetReponse, array('fake'));

        $this->assertNull($this->_restReponse->getContenuReponse());
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40002
     */
    public function testClasseRenderNexistePas()
    {
        $renderFactory = function () {
            return false;
        };

        $this->_restReponse->setRenderFactory($renderFactory);
        $this->_restReponse->setFormatsAcceptes(array('JSON' => 'json'));
        $this->_restReponse->fabriquerReponse($this->getMockObjetReponse(), array('json'));
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40003
     */
    public function testRenderFactoryNonDefini()
    {
        $this->_restReponse->setFormatsAcceptes(array('JSON' => 'json'));
        $this->_restReponse->fabriquerReponse($this->getMockObjetReponse(), array('json'));
    }

    public function testSetObserveurs()
    {
        $abstractDisplayer = $this->getMockAbstractDisplayer();

        $this->_restReponse->setObserveurs(array($abstractDisplayer));
        $this->assertAttributeCount(1, '_observeurs', $this->_restReponse);
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetObserveursErrone()
    {
        $this->_restReponse->setObserveurs(50);
        $this->assertAttributeCount(1, '_observeurs', $this->_restReponse);
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetObserveursErroneDansArray()
    {
        $this->_restReponse->setObserveurs(array(1));
        $this->assertAttributeCount(1, '_observeurs', $this->_restReponse);
    }

    public function testEcrireReponse()
    {
        $headerManager = $this->getMockHeaders();

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('getStatusHttp', 200), new MockArg('getFormat')
        );

        $abstractDisplayer = $this->createMock(
            'AbstractDisplayer', new MockArg('logReponse', null, array($objetReponse))
        );

        $this->_restReponse->setObserveurs(array($abstractDisplayer));

        $this->_restReponse->setHeader($headerManager);
        $this->_restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $this->_restReponse->fabriquerReponse($objetReponse, array('fake'));
    }
}