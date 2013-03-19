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

    public function testRestSetFormatDefaut()
    {
        $this->restReponse->setFormatRetour('JSON');
        $this->assertEquals('JSON', $this->restReponse->getFormatRetour());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetFormatDefautErronee()
    {
        $this->restReponse->setFormatRetour(null);
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
     * @expectedExceptionCode 40001
     */
    public function testRestSetFormatAcceptesVide()
    {
        $this->restReponse->setFormatsAcceptes(array());
    }

    public function testRestSetFormat()
    {
        $this->restReponse->setFormats('PLAIN', array('PLAIN' => 'txt'));
        $this->assertEquals('PLAIN', $this->restReponse->getFormatRetour());
        $this->assertCount(1, $this->restReponse->getFormatsAcceptes());
    }

    public function testRestSetFormatDefautInexistant()
    {
        $this->restReponse->setFormats('PLAIN', array('HTML' => 'html'));
        $this->assertEquals('HTML', $this->restReponse->getFormatRetour());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40001
     */
    public function testRestFormatDefaut()
    {
        $this->restReponse->setFormats('PLAIN', array());
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
     * @expectedExceptionCode 40002
     */
    public function testRestSetCharsetInvalid()
    {
        $this->restReponse->setCharset('UTF-9999999999');
    }

    public function testRestSetConfig()
    {
        $config = $this->createMock(
            'Config', new MockArg('getConfigValeur', 'JSON', array('config.default_render')),
            new MockArg('getConfigValeur', array('JSON' => 'json', 'HTML' => 'html'), array('render')),
            new MockArg('getConfigValeur', 'utf-8', array('config.charset')));

        $this->restReponse->setConfig($config);
        $this->assertEquals('utf-8', $this->restReponse->getCharset());
        $this->assertEquals('JSON', $this->restReponse->getFormatRetour());
        $this->assertCount(2, $this->restReponse->getFormatsAcceptes());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
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
            'AbstractRenderer', new MockArg('genererRendu', '{"getKey":"getVar"}', array(array('getKey' => 'getVar'))));

        /** @var $restReponse ReponseManager|\PHPUnit_Framework_MockObject_MockObject */
        $restReponse = $this->createMock(
            'ReponseManager', new MockArg('getRenderClass', $abstractrender, array('Json')));

        $headerManager = $this->createMock(
            'Header', new MockArg('ajouterHeader'), new MockArg('envoyerHeaders'));

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('getStatusHttp', 200),
            new MockArg('getDonneesReponse', array('getKey' => 'getVar')));

        $restReponse->setHeader($headerManager);
        $restReponse->setFormats('JSON', array('JSON' => 'json'));

        $restReponse->fabriquerReponse($objetReponse, array('json'));

        $this->assertEquals('{"getKey":"getVar"}', $restReponse->getContenu());
        $this->assertEquals('json', $restReponse->getFormatRetour());
    }

    public function testRestRenderNonTrouveUtiliseAutre()
    {
        $abstractrender = $this->createMock(
            'AbstractRenderer', new MockArg('genererRendu', '{"param1":"var1"}', array(array('param1' => 'var1'))));

        /** @var $restReponse ReponseManager|\PHPUnit_Framework_MockObject_MockObject */
        $restReponse = $this->createMock(
            'ReponseManager', new MockArg('getRenderClass', $abstractrender, array('Json')));

        $headerManager = $this->createMock(
            'Header', new MockArg('ajouterHeader'), new MockArg('envoyerHeaders'));

        $objetReponse = $this->createMock(
            'ObjetReponse', new MockArg('getStatusHttp', 200),
            new MockArg('getDonneesReponse', array('param1' => 'var1')));

        $restReponse->setHeader($headerManager);
        $restReponse->setFormats('JSON', array('JSON' => 'json'));

        $restReponse->fabriquerReponse($objetReponse, array('fake'));

        $this->assertEquals('{"param1":"var1"}', $restReponse->getContenu());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40003
     */
    public function testRestRenderNonTrouveDefautNonPlus()
    {
        $this->restReponse->setFormatRetour('HTML');
        $this->restReponse->setFormatsAcceptes(array('JSON' => 'json'));

        $this->restReponse->fabriquerReponse($this->getMockObjetReponse(), array('fake'));
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40004
     */
    public function testRestRenderNonTrouve()
    {
        $this->restReponse->setFormats('FAKE', array('FAKE' => 'fake'));

        $this->restReponse->fabriquerReponse($this->getMockObjetReponse(), array('fake'));
    }
}