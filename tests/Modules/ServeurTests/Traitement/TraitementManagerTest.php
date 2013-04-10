<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Requete\RequeteManager;
use Serveur\Traitement\Data\DatabaseConfig;
use Serveur\Traitement\TraitementManager;
use Tests\MockArg;
use Tests\TestCase;

class TraitementManagerTest extends TestCase
{
    /**
     * @var TraitementManager
     */
    private $_traitementManager;

    public function setUp()
    {
        $this->_traitementManager = new TraitementManager();
    }

    /**
     * @param string $doMethod
     * @param RequeteManager $requete
     */
    public function setFakeDatabase($doMethod, $requete)
    {
        $callableRessourceFactory = function () use ($doMethod, $requete) {
            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg($doMethod, $this->getMockObjetReponse(), array($requete->getUriVariables(),
                    $requete->getParametres()))
            );

            return $abstractRessource;
        };

        $databaseConfig = $this->createMock(
            'DatabaseConfig', new MockArg('getDriver', 'myDriver')
        );

        $callableDatabaseFactory = function () use ($databaseConfig) {
            $this->assertEquals('myDriver', $databaseConfig->getDriver());

            return $this->getMockAbstractDatabase();
        };

        $this->_traitementManager->setRessourceFactory($callableRessourceFactory);
        $this->_traitementManager->setDatabaseFactory($callableDatabaseFactory);
        $this->_traitementManager->setDatabaseConfig($databaseConfig);
    }

    public function testSetFactoryRessource()
    {
        $callable = function () {
        };

        $this->_traitementManager->setRessourceFactory($callable);
        $this->assertAttributeEquals($callable, '_ressourceFactory', $this->_traitementManager);
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testSetFactoryOnlyCallable()
    {
        $this->_traitementManager->setRessourceFactory(array());
    }

    public function testSetDatabaseFactory()
    {
        $databaseFactory = function () {
        };

        $this->_traitementManager->setDatabaseFactory($databaseFactory);
        $this->assertAttributeEquals($databaseFactory, '_databaseFactory', $this->_traitementManager);
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testSetDatabaseFactoryOnlyCallable()
    {
        $this->_traitementManager->setDatabaseFactory(null);
    }

    public function testSetDatabaseConfig()
    {
        $databaseConfig = new DatabaseConfig();

        $this->_traitementManager->setDatabaseConfig($databaseConfig);
        $this->assertAttributeEquals($databaseConfig, '_databaseConfig', $this->_traitementManager);
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testSetDatabaseConfigErrone()
    {
        $this->_traitementManager->setDatabaseConfig(5);
    }

    public function testSetAuthManager()
    {
        $authManager = $this->getMockAuthManager();

        $this->_traitementManager->setAuthManager($authManager);
        $this->assertAttributeEquals($authManager, '_authManager', $this->_traitementManager);
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testSetAuthManagerErrone()
    {
        $this->_traitementManager->setAuthManager(5);
    }

    public function testRecupererRessource()
    {
        $callable = function ($ressName) {
            return $ressName;
        };

        $this->_traitementManager->setRessourceFactory($callable);

        $this->assertEquals(
            'myRessName', $this->_traitementManager->recupererNouvelleInstanceRessource('myRessName')
        );
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30000
     */
    public function testTraiterImpossibleConnexionDatabase()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getUriVariables', array('path', '1'))
        );

        $callable = function () {
            return true;
        };

        $databaseConfig = $this->createMock(
            'DatabaseConfig', new MockArg('getDriver', 'myDriver')
        );

        $callableDatabaseFactory = function () {
            return false;
        };

        $this->_traitementManager->setRessourceFactory($callable);
        $this->_traitementManager->setDatabaseFactory($callableDatabaseFactory);
        $this->_traitementManager->setDatabaseConfig($databaseConfig);

        $this->_traitementManager->traiterRequeteEtRecupererResultat($requete);
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30001
     */
    public function testRecupererSansAvoirSetRessourceFactory()
    {
        $this->_traitementManager->recupererNouvelleInstanceRessource('Gonna bug down');
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30002
     */
    public function testRecupererSansAvoirSetDatabaseFactory()
    {
        $this->_traitementManager->recupererNouvelleInstanceConnexion('Gonna bug down');
    }

    public function testTraiterGet()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'GET'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1'))
        );
        $this->setFakeDatabase('doGet', $requete);

        $this->assertInstanceOf(
            'Serveur\Lib\ObjetReponse', $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterPut()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'PUT'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1'))
        );
        $this->setFakeDatabase('doPut', $requete);

        $this->assertInstanceOf(
            'Serveur\Lib\ObjetReponse', $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterPost()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'POST'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1'))
        );
        $this->setFakeDatabase('doPost', $requete);

        $this->assertInstanceOf(
            'Serveur\Lib\ObjetReponse', $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterDelete()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'DELETE'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1'))
        );
        $this->setFakeDatabase('doDelete', $requete);

        $this->assertInstanceOf(
            'Serveur\Lib\ObjetReponse', $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterRessourceInconnue()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getUriVariables', array('unknown'))
        );

        $callable = function ($arg) {
            $this->assertEquals('unknown', $arg);

            return false;
        };

        $this->_traitementManager->setRessourceFactory($callable);

        $this->assertEquals(
            404, $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
        );
    }

    public function testTraiterRessourceNonInformee()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getUriVariables', array(''))
        );

        $callable = function ($arg) {
            $this->assertEquals('', $arg);

            return false;
        };

        $this->_traitementManager->setRessourceFactory($callable);

        $this->assertEquals(
            400, $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
        );
    }

}