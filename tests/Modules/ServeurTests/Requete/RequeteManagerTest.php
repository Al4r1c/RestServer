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

    private function setFakeServerDonnees($returnValue)
    {
        $mockServer = $this->createMock('Server', new MockArg('getServeurDonnees', $returnValue));
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
        $this->setFakeServerDonnees(array('myArray'));

        $this->assertInternalType('array', $this->restRequete->getParametres());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRestDonneeSeulementTableau()
    {
        $this->setFakeServerDonnees('GO_GO_ERREUR');
        $this->restRequete->getParametres('GO_GO_ERREUR');
    }

    public function testParametreSauvegardes()
    {
        $this->setFakeServerDonnees(array("param1" => "valeur1", "data" => 1));
        $this->assertCount(2, $this->restRequete->getParametres());
    }

    public function testRecupererParametre()
    {
        $this->setFakeServerDonnees(array("param1" => "valeur1", "data" => 1));
        $this->assertEquals('valeur1', $this->restRequete->getParametres()['param1']);
    }

    public function testSetHeaderRequete()
    {
        $headerRequete = $this->getMockRequeteHeaders();
        $this->restRequete->setRequeteHeader($headerRequete);
        $this->assertAttributeEquals($headerRequete, '_requeteHeader', $this->restRequete);
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testSetHeaderRequeteErrone()
    {
        $this->restRequete->setRequeteHeader(array());
    }

    public function testGetUnHeader()
    {
        $headerRequete =
            $this->createMock(
                'RequeteHeaders', new MockArg('getHeaders', array('Host' => 'http://www.somewhere.com/'))
            );
        $this->restRequete->setRequeteHeader($headerRequete);
        $this->assertEquals('http://www.somewhere.com/', $this->restRequete->getUnHeader('Host'));
    }

    public function testGetUnHeaderIUnexistantDonneNull()
    {
        $headerRequete =
            $this->createMock(
                'RequeteHeaders', new MockArg('getHeaders', array('Host' => 'http://www.somewhere.com/'))
            );
        $this->restRequete->setRequeteHeader($headerRequete);
        $this->assertNull($this->restRequete->getUnHeader('Date'));
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
}