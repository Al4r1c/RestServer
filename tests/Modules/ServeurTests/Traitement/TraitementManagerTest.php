<?php
namespace Tests\ServeurTests\Traitement;

use AlaroxRestServeur\Serveur\Requete\RequeteManager;
use AlaroxRestServeur\Serveur\Traitement\Data\DatabaseConfig;
use AlaroxRestServeur\Serveur\Traitement\TraitementManager;
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
    public function setFakeDatabaseAuthOk($doMethod, $requete)
    {
        $callableRessourceFactory = function () use ($doMethod, $requete) {
            $mock = $this->getMockAbstractRessource(array($doMethod));

            $mock->expects($this->once())
                ->method($doMethod)
                ->with(
                    $this->equalTo($requete->getUriVariables()),
                    $this->isInstanceOf('\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager')
                )
                ->will($this->returnValue($this->getMockObjetReponse()));

            return $mock;
        };

        $databaseConfig = $this->createMock(
            'DatabaseConfig', new MockArg('getDriver', 'myDriver')
        );

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', false),
            new MockArg('isAuthActivated', false)
        );

        $callableDatabaseFactory = function () use ($databaseConfig) {
            $this->assertEquals('myDriver', $databaseConfig->getDriver());

            return $this->getMockAbstractDatabase();
        };

        $this->_traitementManager->setRessourceFactory($callableRessourceFactory);
        $this->_traitementManager->setDatabaseFactory($callableDatabaseFactory);
        $this->_traitementManager->setDatabaseConfig($databaseConfig);
        $this->_traitementManager->setAuthManager($authManager);
    }

    public function testSetFactoryRessource()
    {
        $callable = function () {
        };

        $this->_traitementManager->setRessourceFactory($callable);
        $this->assertAttributeEquals($callable, '_ressourceFactory', $this->_traitementManager);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
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
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
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
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
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
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
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
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30001
     */
    public function testTraiterImpossibleConnexionDatabase()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getUriVariables', array('path', '1')),
            new MockArg('getDateRequete')
        );

        $callable = function () {
            return true;
        };

        $databaseConfig = $this->createMock(
            'DatabaseConfig', new MockArg('getDriver', 'myDriver')
        );

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', false),
            new MockArg('isAuthActivated', false)
        );

        $callableDatabaseFactory = function () {
            return false;
        };

        $this->_traitementManager->setRessourceFactory($callable);
        $this->_traitementManager->setDatabaseFactory($callableDatabaseFactory);
        $this->_traitementManager->setDatabaseConfig($databaseConfig);
        $this->_traitementManager->setAuthManager($authManager);

        $this->_traitementManager->traiterRequeteEtRecupererResultat($requete);
    }

    public function testTraiterGet()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'GET'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1')),
            new MockArg('getDateRequete')
        );
        $this->setFakeDatabaseAuthOk('doGet', $requete);

        $this->assertInstanceOf(
            'AlaroxRestServeur\Serveur\Lib\ObjetReponse',
            $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterPut()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'PUT'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1')),
            new MockArg('getDateRequete')
        );
        $this->setFakeDatabaseAuthOk('doPut', $requete);

        $this->assertInstanceOf(
            'AlaroxRestServeur\Serveur\Lib\ObjetReponse',
            $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterPost()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'POST'),
            new MockArg('getParametres', array('data1' => 'var1')),
            new MockArg('getUriVariables', array('path', '1')),
            new MockArg('getDateRequete')
        );
        $this->setFakeDatabaseAuthOk('doPost', $requete);

        $this->assertInstanceOf(
            'AlaroxRestServeur\Serveur\Lib\ObjetReponse',
            $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterDelete()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', 'DELETE'),
            new MockArg('getUriVariables', array('path', '1')),
            new MockArg('getDateRequete')
        );

        $callableRessourceFactory = function () use ($requete) {
            $mock = $this->getMockAbstractRessource(array('doDelete'));

            $mock->expects($this->once())
                ->method('doDelete')
                ->with($this->equalTo($requete->getUriVariables()))
                ->will($this->returnValue($this->getMockObjetReponse()));

            return $mock;
        };

        $databaseConfig = $this->createMock(
            'DatabaseConfig', new MockArg('getDriver', 'myDriver')
        );

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', false),
            new MockArg('isAuthActivated', false)
        );

        $callableDatabaseFactory = function () use ($databaseConfig) {
            $this->assertEquals('myDriver', $databaseConfig->getDriver());

            return $this->getMockAbstractDatabase();
        };

        $this->_traitementManager->setRessourceFactory($callableRessourceFactory);
        $this->_traitementManager->setDatabaseFactory($callableDatabaseFactory);
        $this->_traitementManager->setDatabaseConfig($databaseConfig);
        $this->_traitementManager->setAuthManager($authManager);


        $this->assertInstanceOf(
            'AlaroxRestServeur\Serveur\Lib\ObjetReponse',
            $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)
        );
    }

    public function testTraiterRessourceInconnue()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getUriVariables', array('unknown')),
            new MockArg('getDateRequete')
        );

        $callable = function ($arg) {
            $this->assertEquals('unknown', $arg);

            return false;
        };

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', false),
            new MockArg('isAuthActivated', false)
        );

        $this->_traitementManager->setRessourceFactory($callable);
        $this->_traitementManager->setDatabaseFactory(
            function () {
            }
        );
        $this->_traitementManager->setDatabaseConfig($this->getMockDatabaseConfig());
        $this->_traitementManager->setAuthManager($authManager);

        $this->assertEquals(
            404, $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
        );
    }

    public function testTraiterRessourceNonInformee()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getUriVariables', array('')),
            new MockArg('getDateRequete')
        );

        $callable = function ($arg) {
            $this->assertEquals('', $arg);

            return false;
        };

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', false),
            new MockArg('isAuthActivated', false)
        );

        $this->_traitementManager->setRessourceFactory($callable);
        $this->_traitementManager->setDatabaseFactory(
            function () {
            }
        );
        $this->_traitementManager->setDatabaseConfig($this->getMockDatabaseConfig());
        $this->_traitementManager->setAuthManager($authManager);

        $this->assertEquals(
            400, $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
        );
    }

    public function testTraiterAvecAuthNOk()
    {
        $requete = $this->getMockRestRequete(array('getDateRequete'));

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', false),
            new MockArg('isAuthActivated', true),
            new MockArg('authentifier', false)
        );


        $this->_traitementManager->setRessourceFactory(
            function () {
            }
        );
        $this->_traitementManager->setDatabaseFactory(
            function () {
            }
        );
        $this->_traitementManager->setDatabaseConfig($this->getMockDatabaseConfig());
        $this->_traitementManager->setAuthManager($authManager);

        $this->assertEquals(
            401, $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
        );
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30000
     */
    public function testTraiterInstanceManquante()
    {
        $this->_traitementManager->traiterRequeteEtRecupererResultat($this->getMockRestRequete());
    }

    public function testTraiterRequeteExpired()
    {
        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getDateRequete', $time = new \DateTime())
        );

        $authManager = $this->createMock(
            'AuthManager',
            new MockArg('hasExpired', true, array($time))
        );

        $this->_traitementManager->setRessourceFactory(
            function () {
            }
        );
        $this->_traitementManager->setDatabaseFactory(
            function () {
            }
        );
        $this->_traitementManager->setDatabaseConfig($this->getMockDatabaseConfig());
        $this->_traitementManager->setAuthManager($authManager);

        $this->assertEquals(
            410, $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
        );
    }
}