<?php
    namespace Tests\ServeurTests\Traitement;

    use Tests\TestCase;
    use Tests\MockArg;
    use Serveur\Requete\RequeteManager;
    use Serveur\Traitement\TraitementManager;

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

        public function setFakeCallable($mockArg)
        {
            $callable = function () use ($mockArg) {
                $abstractRessource = $this->createMock('AbstractRessource', $mockArg);

                return $abstractRessource;
            };

            $this->_traitementManager->setFactoryRessource($callable);
        }

        public function testSetFactoryRessource()
        {
            $callable = function () {
            };

            $this->_traitementManager->setFactoryRessource($callable);
            $this->assertAttributeEquals($callable, '_factoryRessource', $this->_traitementManager);
        }

        /**
         * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         */
        public function testSetFactoryOnlyCallable()
        {
            $this->_traitementManager->setFactoryRessource(array());
        }

        public function testRecupererRessource()
        {
            $callable = function ($ressName) {
                return $ressName;
            };

            $this->_traitementManager->setFactoryRessource($callable);

            $this->assertEquals(
                'myRessName',
                $this->_traitementManager->recupererNouvelleInstanceRessource('myRessName')
            );
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
            $requete = new RequeteManager();
            $requete->setMethode('GET');
            $requete->setParametres(array('data1' => 'var1'));
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $this->setFakeCallable(new MockArg('doGet', $objetReponse, array(1, array('data1' => 'var1'))));

            $this->assertEquals(
                array('here' => 'some data'),
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterPut()
        {
            $requete = new RequeteManager();
            $requete->setMethode('PUT');
            $requete->setParametres(array('data1' => 'var1'));
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $this->setFakeCallable(new MockArg('doPut', $objetReponse, array(1, array('data1' => 'var1'))));

            $this->assertEquals(
                array('here' => 'some data'),
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterPost()
        {
            $requete = new RequeteManager();
            $requete->setMethode('POST');
            $requete->setParametres(array('data1' => 'var1'));
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $this->setFakeCallable(new MockArg('doPost', $objetReponse, array(1, array('data1' => 'var1'))));

            $this->assertEquals(
                array('here' => 'some data'),
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterDelete()
        {
            $requete = new RequeteManager();
            $requete->setMethode('DELETE');
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $this->setFakeCallable(new MockArg('doDelete', $objetReponse, array(1)));

            $this->assertEquals(
                array('here' => 'some data'),
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterRessourceInconnue()
        {
            $requete = new RequeteManager();
            $requete->setMethode('GET');
            $requete->setVariableUri('/unknown');

            $callable = function ($arg) {
                $this->assertEquals('unknown', $arg);

                return false;
            };

            $this->_traitementManager->setFactoryRessource($callable);

            $this->assertEquals(
                404,
                $this->_traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp()
            );
        }

        /**
         * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 30000
         */
        public function testTraiterMethodeInconnue()
        {
            $requete = $this->createMock(
                'RequeteManager',
                new MockArg('getMethode', 'FAKE')
            );
            $requete->setVariableUri('/path/1');

            $callable = function ($arg) {
                $this->assertEquals('path', $arg);

                return true;
            };

            $this->_traitementManager->setFactoryRessource($callable);

            $this->_traitementManager->traiterRequeteEtRecupererResultat($requete);
        }
    }