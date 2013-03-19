<?php
    namespace Tests\ServeurTests\Traitement;

    use Serveur\Requete\RequeteManager;
    use Serveur\Traitement\Data\DatabaseConfig;
    use Serveur\Traitement\Data\DatabaseFactory;
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
         * @param string $methode
         * @param array $param
         * @param string $vuri
         * @return RequeteManager
         */
        public function getRequete($methode, $vuri, $param = array())
        {
            $requete = new RequeteManager();
            $requete->setMethode($methode);
            $requete->setParametres($param);
            $requete->setVariableUri($vuri);

            return $requete;
        }

        /**
         * @param string $doMethod
         * @param RequeteManager $requete
         */
        public function setFakeDatabase($doMethod, $requete)
        {
            $callable = function () use ($doMethod, $requete) {
                $abstractRessource = $this->createMock('AbstractRessource',
                    new MockArg($doMethod, $this->getMockObjetReponse(), array($requete->getUriVariables(),
                        $requete->getParametres())));

                return $abstractRessource;
            };

            $databaseConfig = $this->createMock('DatabaseConfig',
                new MockArg('getDriver', 'myDriver'));

            $abstractRessource = $this->getMockAbstractDatabase();

            $databaseFactory = $this->createMock('DatabaseFactory',
                new MockArg('getConnexionDatabase', $abstractRessource, array('myDriver')));

            $this->_traitementManager->setRessourceFactory($callable);
            $this->_traitementManager->setDatabaseFactory($databaseFactory);
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
            $databaseFactory = new DatabaseFactory();

            $this->_traitementManager->setDatabaseFactory($databaseFactory);
            $this->assertAttributeEquals($databaseFactory, '_databaseFactory', $this->_traitementManager);
        }

        /**
         * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         */
        public function testSetDatabaseFactoryErrone()
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

        public function testRecupererRessource()
        {
            $callable = function ($ressName) {
                return $ressName;
            };

            $this->_traitementManager->setRessourceFactory($callable);

            $this->assertEquals('myRessName',
                $this->_traitementManager->recupererNouvelleInstanceRessource('myRessName'));
        }

        /**
         * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 30000
         */
        public function testTraiterImpossibleConnexionDatabase()
        {
            $requete = $this->getRequete('GET', '/path/1');

            $callable = function () {
                return true;
            };

            $databaseConfig = $this->createMock('DatabaseConfig',
                new MockArg('getDriver', 'myDriver'));

            $databaseFactory = $this->createMock('DatabaseFactory',
                new MockArg('getConnexionDatabase', false, array('myDriver')));

            $this->_traitementManager->setRessourceFactory($callable);
            $this->_traitementManager->setDatabaseFactory($databaseFactory);
            $this->_traitementManager->setDatabaseConfig($databaseConfig);

            $this->_traitementManager->traiterRequeteEtRecupererResultat($requete);
        }

        /**
         * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 30001
         */
        public function testRecupererSansAvoirSetFactory()
        {
            $this->_traitementManager->recupererNouvelleInstanceRessource('Gonna bug down');
        }

        public function testTraiterGet()
        {
            $requete = $this->getRequete('GET', '/path/1', array('data1' => 'var1'));
            $this->setFakeDatabase('doGet', $requete);

            $this->assertInstanceOf('Serveur\Lib\ObjetReponse',
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete));
        }

        public function testTraiterPut()
        {
            $requete = $this->getRequete('PUT', '/path/1', array('data1' => 'var1'));
            $this->setFakeDatabase('doPut', $requete);

            $this->assertInstanceOf('Serveur\Lib\ObjetReponse',
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete));
        }

        public function testTraiterPost()
        {
            $requete = $this->getRequete('POST', '/path/1', array('data1' => 'var1'));
            $this->setFakeDatabase('doPost', $requete);

            $this->assertInstanceOf('Serveur\Lib\ObjetReponse',
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete));
        }

        public function testTraiterDelete()
        {
            $requete = $this->getRequete('DELETE', '/path/1', array('data1' => 'var1'));
            $this->setFakeDatabase('doDelete', $requete);

            $this->assertInstanceOf('Serveur\Lib\ObjetReponse',
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete));
        }

        public function testTraiterRessourceInconnue()
        {
            $requete = $this->getRequete('GET', '/unknown');

            $callable = function ($arg) {
                $this->assertEquals('unknown', $arg);

                return false;
            };

            $this->_traitementManager->setRessourceFactory($callable);

            $this->assertEquals(404,
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp());
        }
    }