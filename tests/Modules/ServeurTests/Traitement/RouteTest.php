<?php
    namespace Modules\ServeurTests\Config;

    use Modules\TestCase;
    use Modules\MockArg;
    use Serveur\Traitement\Route\Route;

    class RouteManagerTest extends TestCase
    {
        /** @var Route */
        private $_route;

        public function setUp()
        {
            $this->_route = new Route();
        }

        public function testSetRoute()
        {
            $this->_route->setRoutesListe(array('/route' => 'routeRess'));

            $this->assertEquals(array('/route' => 'routeRess'), $this->_route->getRoutesListe());
        }

        public function testSetRouteVide()
        {
            $this->_route->setRoutesListe(array());

            $this->assertEquals(array(), $this->_route->getRoutesListe());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetRouteArray()
        {
            $this->_route->setRoutesListe(null);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 30100
         */
        public function testSetRouteInvalide()
        {
            $this->_route->setRoutesListe(array('route' => 'routeRess'));
        }

        public function testChargerFichier()
        {
            $fichier = $this->createMock(
                'Fichier',
                new MockArg('chargerFichier', array('/uneRoute' => 'uneRessource'))
            );

            $this->_route->chargerFichierMapping($fichier);

            $this->assertEquals(array('/uneRoute' => 'uneRessource'), $this->_route->getRoutesListe());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testChargerFichierErrone()
        {
            $this->_route->chargerFichierMapping(null);
        }

        public function testGetUneRoute()
        {
            $this->_route->setRoutesListe(array('/routeOne' => 'ressOne', '/routeNew' => 'ressNew'));

            $this->assertEquals('ressOne', $this->_route->getUneRoute('/routeOne'));
        }

        public function testGetUneRouteNonTrouvee()
        {
            $this->_route->setRoutesListe(array('/routeOne' => 'ressOne', '/routeNew' => 'ressNew'));

            $this->assertNull($this->_route->getUneRoute('/fakeRoute'));
        }
    }