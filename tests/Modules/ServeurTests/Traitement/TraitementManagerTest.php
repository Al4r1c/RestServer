<?php
    namespace Modules\ServeurTests\Config;

    use Modules\TestCase;
    use Modules\MockArg;
    use Serveur\Traitement\TraitementManager;

    class TraitementManagerTest extends TestCase
    {
        /** @var TraitementManager */
        private $_traitementManager;

        public function setUp()
        {
            $this->_traitementManager = new TraitementManager();
        }

        public function testSetRoute() {
            $routeMap = $this->createMock('RouteMap');

            $this->_traitementManager->setRouteMap($routeMap);

            $this->assertEquals($routeMap, $this->_traitementManager->getRouteMap());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetRouteErrone()
        {
            $this->_traitementManager->setRouteMap('fake');
        }
    }