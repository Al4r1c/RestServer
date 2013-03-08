<?php
    namespace Modules\ServeurTests\Config;

    use Modules\TestCase;
    use Modules\MockArg;

    class RouteManagerTest extends TestCase
    {
        /** @var \Serveur\Route\RouteManager */
        private $_routeManager;

        public function setUp()
        {
            $this->_routeManager = new \Serveur\Route\RouteManager();
        }

        public function testSetRoute()
        {
            $this->_routeManager->setRoutesListe(array('/route' => 'routeRess'));

            $this->assertEquals(array('/route' => 'routeRess'), $this->_routeManager->getRoutesListe());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetRouteArray()
        {
            $this->_routeManager->setRoutesListe(null);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\MainException
         * @expectedExceptionCode 30100
         */
        public function testSetRouteInvalide()
        {
            $this->_routeManager->setRoutesListe(array('route' => 'routeRess'));
        }

        public function testChargerFichier()
        {
            $fichier = $this->createMock(
                'Fichier',
                new MockArg('chargerFichier', array('/uneRoute' => 'uneRessource'))
            );

            $this->_routeManager->chargerFichierMapping($fichier);

            $this->assertEquals(array('/uneRoute' => 'uneRessource'), $this->_routeManager->getRoutesListe());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testChargerFichierErrone()
        {
            $this->_routeManager->chargerFichierMapping(null);
        }

        public function testGetUneRoute()
        {
            $this->_routeManager->setRoutesListe(array('/routeOne' => 'ressOne', '/routeNew' => 'ressNew'));

            $this->assertEquals('ressOne', $this->_routeManager->getUneRoute('/routeOne'));
        }

        public function testGetUneRouteNonTrouvee()
        {
            $this->_routeManager->setRoutesListe(array('/routeOne' => 'ressOne', '/routeNew' => 'ressNew'));

            $this->assertNull($this->_routeManager->getUneRoute('/fakeRoute'));
        }
    }