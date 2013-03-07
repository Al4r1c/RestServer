<?php
    namespace Modules\ServeurTests\Application;

    use Serveur\MainApplication;
    use Conteneur\MonConteneur;

    class MainApplicationTest extends \PHPUnit_Framework_TestCase {

        protected $mainApp;

        protected function setUp() {
            $conteneur = new MonConteneur();
            $this->mainApp = new MainApplication($conteneur);
        }

        public function testObjetCree() {
            $this->assertThat($this->mainApp,
                $this->logicalAnd($this->logicalNot($this->isNull()), $this->isInstanceOf('Serveur\MainApplication')));
        }
    }