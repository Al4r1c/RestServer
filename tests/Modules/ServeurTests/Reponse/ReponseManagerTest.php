<?php
namespace Tests\ServeurTests\Reponse;

use Serveur\Reponse\ReponseManager;
use Tests\MockArg;
use Tests\TestCase;

class ReponseManagerTest extends TestCase
{
    /**
     * @var ReponseManager $restRequete
     */
    private $restReponse;

    protected function setUp()
    {
        $this->restReponse = new ReponseManager();
    }

    public function testRestSetHeader()
    {
        $header = $this->getMockHeaders();

        $this->restReponse->setHeader($header);
        $this->assertAttributeEquals($header, '_header', $this->restReponse);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetHeaderInvalide()
    {
        $this->restReponse->setHeader(null);
    }


    public function testRestSetFormatAcceptes()
    {
        $this->restReponse->setFormatsAcceptes(array('JSON' => 'json', 'TEXT' => 'txt'));
        $this->assertCount(2, $this->restReponse->getFormatsAcceptes());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetFormatAcceptesInvalid()
    {
        $this->restReponse->setFormatsAcceptes('ERROR');
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40000
     */
    public function testRestSetFormatAcceptesVide()
    {
        $this->restReponse->setFormatsAcceptes(array());
    }

    public function testRestSetCharset()
    {
        $this->restReponse->setCharset('utf-8');
        $this->assertEquals('utf-8', $this->restReponse->getCharset());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetCharsetInvalide()
    {
        $this->restReponse->setCharset(9);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40001
     */
    public function testRestSetCharsetInvalid()
    {
        $this->restReponse->setCharset('UTF-9999999999');
    }

    public function testRestSetConfig()
    {
        $config = $this->createMock(
            'Config', new MockArg('getConfigValeur', array('JSON' => 'json', 'HTML' => 'html'), array('render')),
            new MockArg('getConfigValeur', 'utf-8', array('config.charset'))
        );

        $this->restReponse->setConfig($config);
        $this->assertEquals('utf-8', $this->restReponse->getCharset());
        $this->assertCount(2, $this->restReponse->getFormatsAcceptes());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetConfigInvalide()
    {
        $this->restReponse->setConfig(null);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestRenderBug()
    {
        $this->restReponse->fabriquerReponse($this->getMockObjetReponse(), 'boum');
    }

    public function testRestRenderAbstract()
    {
        $abstractrender = $this->createMock(
            'AbstractRenderer', new MockArg('genererRendu', '{"getKey":"getVar"}', array(array('getKey' => 'getVar')))
        );

        /** @var $restReponse ReponseManager|\PHPUnit_Framework_MockObject_MockObject */
        $restReponse = $this->createMock(
            'ReponseManager', new MockArg('getRenderClass', $abstractrender, array('Json'))
        );

        $headerManager = $this->getMockHeaders();

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('getStatusHttp', 200),
            new MockArg('getDonneesReponse', array('getKey' => 'getVar'))
        );

        $restReponse->setHeader($headerManager);
        $restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $restReponse->fabriquerReponse($objetReponse, array('json'));

        $this->assertEquals('{"getKey":"getVar"}', $restReponse->getContenuReponse());
    }

    public function testRestRenderNonTrouveRenvoi406()
    {
        $headerManager = $this->getMockHeaders();

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('setErreurHttp', 406)
        );

        $this->restReponse->setHeader($headerManager);
        $this->restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $this->restReponse->fabriquerReponse($objetReponse, array('fake'));

        $this->assertNull($this->restReponse->getContenuReponse());
    }

    public function testSetObserveurs()
    {
        $abstractDisplayer = $this->getMockAbstractDisplayer();

        $this->restReponse->setObserveurs(array($abstractDisplayer));
        $this->assertAttributeCount(1, '_observeurs', $this->restReponse);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetObserveursErrone()
    {
        $this->restReponse->setObserveurs(50);
        $this->assertAttributeCount(1, '_observeurs', $this->restReponse);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetObserveursErroneDansArray()
    {
        $this->restReponse->setObserveurs(array(1));
        $this->assertAttributeCount(1, '_observeurs', $this->restReponse);
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

        $this->restReponse->setObserveurs(array($abstractDisplayer));

        $this->restReponse->setHeader($headerManager);
        $this->restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $this->restReponse->fabriquerReponse($objetReponse, array('fake'));
    }
}