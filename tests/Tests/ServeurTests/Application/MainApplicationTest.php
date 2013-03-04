<?php
	namespace Tests\ServeurTests\Application;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Serveur\MainApplication;
	use Conteneur\MonConteneur;

	class MainApplicationTest extends \PHPUnit_Framework_TestCase {

		protected $mainApp;

		protected function setUp() {
			$conteneur = new MonConteneur();
			$this->mainApp = new MainApplication($conteneur);
		}

		public function testObjetCree() {
			$this->assertThat(
				$this->mainApp,
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\MainApplication')
				)
			);
		}
	}