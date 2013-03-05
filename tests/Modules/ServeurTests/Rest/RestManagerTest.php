<?php
	namespace Modules\ServeurTests\Rest;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Modules\TestCase;
	use Serveur\Rest\RestManager;

	class RestManagerTest extends TestCase {
		/**
		 * @var RestManager
		 */
		private $restManager;

		public function setUp() {
			$this->restManager = new RestManager();
		}

		public function testRestSetRequete() {
			$restRequete = $this->getMockRestRequete();
			$this->restManager->setRequete($restRequete);

			$this->assertEquals($restRequete, $this->restManager->getRestRequest());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestSetRequeteNull() {
			$this->restManager->setRequete(null);
		}

		public function testRestSetReponse() {
			$restReponse = $this->getMockRestReponse();

			$this->restManager->setReponse($restReponse);

			$this->assertEquals($restReponse, $this->restManager->getRestResponse());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestSetReponseNull() {
			$this->restManager->setReponse(null);
		}

		public function testRestRecupererUriParam() {
			$restRequete = $this->createMock('RestRequete',
				array('getUriVariables', '', array('0' => 'monuri'))
			);

			$this->restManager->setRequete($restRequete);

			$this->assertEquals('monuri', $this->restManager->getUriVariable(0));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestUriVariableNonInt() {
			$this->restManager->getUriVariable('clef');
		}

		public function testRestUriVariableNull() {
			$restRequete = $this->createMock('RestRequete',
				array('getUriVariables', '', array('0' => 'monuri'))
			);

			$this->restManager->setRequete($restRequete);

			$this->assertNull($this->restManager->getUriVariable(1));
		}

		public function testRestRecupererDonnee() {
			$restRequete = $this->createMock('RestRequete',
				array('getParametres', '', array('param1' => 'donnee1'))
			);

			$this->restManager->setRequete($restRequete);

			$this->assertEquals('donnee1', $this->restManager->getParametre('param1'));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestRecupererDonneeClefNonString() {
			$this->restManager->getParametre(50);
		}

		public function testRestRenvoieDonneeNull() {
			$restRequete = $this->createMock('RestRequete',
				array('getParametres', '', array('param1' => 'donnee1'))
			);


			$this->restManager->setRequete($restRequete);

			$this->assertNull($this->restManager->getParametre('param2'));
		}

		public function testRestSetVariableReponse() {
			$restReponse = $this->createMock('RestReponse',
				array('setStatus', 500),
				array('setContenu', "<html></html>")
			);


			$this->restManager->setReponse($restReponse);

			$this->restManager->setVariablesReponse(500, "<html></html>");
		}

		public function testRestFabriquerReponse() {
			$restRequete = $this->createMock('RestRequete',
				array('getFormatsDemandes', '', array('json'))
			);

			$restReponse = new \Serveur\Rest\RestReponse();
			$restReponse->setContenu(array('param1' => 'var1'));
			$restReponse->setFormats('JSON', array('JSON' => 'json'));
			$headerManager = $this->createMock('HeaderManager',
				array('ajouterHeader'),
				array('envoyerHeaders')
			);

			$restReponse->setHeaderManager($headerManager);


			$this->restManager->setRequete($restRequete);
			$this->restManager->setReponse($restReponse);

			$this->assertEquals('{"param1":"var1"}', $this->restManager->fabriquerReponse(array('json')));
		}
	}