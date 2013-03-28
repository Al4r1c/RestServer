<?php
namespace Tests\ServeurTests\Requete;

use Serveur\Requete\RequeteManager;
use Tests\MockArg;
use Tests\TestCase;

class RequeteManagerTest extends TestCase
{

    /**
     * @var RequeteManager $restRequete
     */
    private $restRequete;

    protected function setUp()
    {
        $this->restRequete = new RequeteManager();
    }

    private function setFakeServerVariables($clef, $returnValue)
    {
        $mockServer = $this->createMock('Server', new MockArg('getUneVariableServeur', $returnValue, array($clef)));
        $this->restRequete->setServer($mockServer);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20000
     */
    public function testRestMethodeValide()
    {
        $this->setFakeServerVariables('REQUEST_METHOD', 'BUGS');

        $this->restRequete->getMethode('METHODE_ERREUR');
    }

    public function testRestMethodeAcceptePost()
    {
        $this->setFakeServerVariables('REQUEST_METHOD', 'POST');

        $this->assertEquals('POST', $this->restRequete->getMethode());
    }

    public function testRestAcceptFormatJSON()
    {
        $this->setFakeServerVariables('HTTP_ACCEPT', 'application/json');

        $this->assertContains('json', $this->restRequete->getFormatsDemandes());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestAcceptFormatInvalide()
    {
        $this->setFakeServerVariables('HTTP_ACCEPT', 5);

        $this->restRequete->getFormatsDemandes();
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20001
     */
    public function testRestFormatValide()
    {
        $this->setFakeServerVariables('HTTP_ACCEPT', 'HTTP_ACCEPT_INVALIDE');

        $this->restRequete->getFormatsDemandes();
    }

    public function testRestUri()
    {
        $this->setFakeServerVariables('REQUEST_URI', '/mon/uri/');

        $this->assertInternalType('array', $this->restRequete->getUriVariables());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestUriErronee()
    {
        $this->setFakeServerVariables('REQUEST_URI', 20.2);

        $this->restRequete->getUriVariables();
    }

    public function testgetUriVariable()
    {
        $this->setFakeServerVariables('REQUEST_URI', '/mon/uri/');

        $this->assertEquals('uri', $this->restRequete->getUriVariable(1));
    }

    public function testgetUriVariableNullSiNonTrouve()
    {
        $this->setFakeServerVariables('REQUEST_URI', '/mon/uri/');

        $this->assertNull($this->restRequete->getUriVariable(3));
    }

    public function testRestUriRecupererVariable()
    {
        $this->setFakeServerVariables('REQUEST_URI', '/variable1//var2////var3/');

        $this->assertEquals('variable1', $this->restRequete->getUriVariables()[0]);
        $this->assertEquals('var3', $this->restRequete->getUriVariables()[2]);
    }

    public function testRestRessourceEncode()
    {
        $this->setFakeServerVariables('REQUEST_URI', '/rés%s"ou#rce<////');

        $this->assertEquals(rawurlencode('rés%s"ou#rce<'), $this->restRequete->getUriVariables()[0]);
    }

    public function testRestRessourceNoVariable()
    {
        $this->setFakeServerVariables('REQUEST_URI', '/var1?id=3');

        $this->assertEquals(rawurlencode('var1'), $this->restRequete->getUriVariables()[0]);
    }

    public function testRestDonnee()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'GET', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', '', array('QUERY_STRING'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertInternalType('array', $this->restRequete->getParametres());
    }

    public function testParametreSauvegardes()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'GET', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', 'param1=valeur1&data=1', array('QUERY_STRING'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertCount(2, $this->restRequete->getParametres());
    }

    public function testRecupererParametre()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'GET', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', 'param1=valeur1&data=1', array('QUERY_STRING'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals('valeur1', $this->restRequete->getParametres()['param1']);
    }

    public function testRecupererPost()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'POST', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', null, array('CONTENT_TYPE')),
            new MockArg('getUneVariableServeur', 'once=var1&twice=var2', array('PHP_INPUT'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals(array('once' => 'var1', 'twice' => 'var2'), $this->restRequete->getParametres());
    }

    public function testRecupererPut()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'PUT', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', null, array('CONTENT_TYPE')),
            new MockArg('getUneVariableServeur', 'once=var1&twice=var2', array('PHP_INPUT'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals(array('once' => 'var1', 'twice' => 'var2'), $this->restRequete->getParametres());
    }

    public function testRecupererDelete()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'DELETE', array('REQUEST_METHOD'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals(array(), $this->restRequete->getParametres());
    }

    public function testRestSetServer()
    {
        $serveur = $this->createMock('Server');

        $this->restRequete->setServer($serveur);

        $this->assertAttributeEquals($serveur, '_server', $this->restRequete);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestSetServerEronnee()
    {
        $this->restRequete->setServer(null);
    }

    public function testRestDateRequete()
    {
        $this->setFakeServerVariables('REQUEST_TIME', 1362000000);

        $this->assertInstanceOf('DateTime', $this->restRequete->getDateRequete());
        $this->assertEquals($this->restRequete->getDateRequete()->getTimestamp(), 1362000000);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestDateRequeteErrone()
    {
        $this->setFakeServerVariables('REQUEST_TIME', 'oops');

        $this->restRequete->getDateRequete();
    }

    public function testRestIp()
    {
        $this->setFakeServerVariables('REMOTE_ADDR', '192.168.0.250');

        $this->assertEquals('192.168.0.250', $this->restRequete->getIp());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestIpErrone()
    {
        $this->setFakeServerVariables('REMOTE_ADDR', 500);

        $this->restRequete->getIp();
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20002
     */
    public function testRestIpFake()
    {
        $this->setFakeServerVariables('REMOTE_ADDR', 'WRONG_IP');

        $this->restRequete->getIp();
    }

    public function testRestIpV6()
    {
        $this->setFakeServerVariables('REMOTE_ADDR', '8000::123:4567:89AB:CDEF');

        $this->assertEquals('8000::123:4567:89AB:CDEF', $this->restRequete->getIp());
    }

    public function testLogRequete()
    {
        $abstractDisplayer = $this->createMock(
            'AbstractDisplayer', new MockArg('logRequete', null, array($this->restRequete))
        );

        $this->restRequete->logRequete(array($abstractDisplayer));
    }

    public function testGetContentType()
    {
        $this->setFakeServerVariables('CONTENT_TYPE', 'application/xml');

        $this->assertEquals('application/xml', $this->restRequete->getContentType());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20003
     */
    public function testGetContentTypeErrone()
    {
        $this->setFakeServerVariables('CONTENT_TYPE', 'fake_one');

        $this->restRequete->getContentType();
    }

    public function testGetContentTypeEtoileDevientPlain()
    {
        $this->setFakeServerVariables('CONTENT_TYPE', '*/*');

        $this->assertEquals('text/plain', $this->restRequete->getContentType());
    }

    public function testRecupererPutPostContentTypeSet()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'POST', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', 'text/plain', array('CONTENT_TYPE')),
            new MockArg('getUneVariableServeur', 'once=var1&twice=var2', array('PHP_INPUT'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals(array('once' => 'var1', 'twice' => 'var2'), $this->restRequete->getParametres());
    }

    public function testRecupererPutPostContentTypeJson()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'POST', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', 'application/json', array('CONTENT_TYPE')),
            new MockArg('getUneVariableServeur', '{"once":"var1","twice":"var2"}', array('PHP_INPUT'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals(array('once' => 'var1', 'twice' => 'var2'), $this->restRequete->getParametres());
    }

    public function testRecupererPutPostContentTypeXML()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'POST', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', 'application/xml', array('CONTENT_TYPE')),
            new MockArg('getUneVariableServeur', '<root><once>var1</once><twice>var2</twice></root>', array('PHP_INPUT'))
        );
        $this->restRequete->setServer($mockServer);

        $this->assertEquals(
            array('root' => array('attr' => array(),
                'children' => array(
                    array('once' => array('attr' => array(), 'value' => 'var1')),
                    array('twice' => array('attr' => array(), 'value' => 'var2')),
                ))), $this->restRequete->getParametres()
        );
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 20004
     */
    public function testRecupererPutPostContentTypeInconnu()
    {
        $mockServer = $this->createMock(
            'Server',
            new MockArg('getUneVariableServeur', 'POST', array('REQUEST_METHOD')),
            new MockArg('getUneVariableServeur', 'image/jpeg', array('CONTENT_TYPE'))
        );
        $this->restRequete->setServer($mockServer);

        $this->restRequete->getParametres();
    }
}