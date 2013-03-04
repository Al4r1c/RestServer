<?php
	namespace Tests\ServeurTests\Rest;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Rest\RestManager;

	class RestManagerTest extends TestCase {
		public function testRestSetRequete() {
			$restRequete = $this->getMockRestRequete();
			$restManager = new RestManager();
			$restManager->setRequete($restRequete);

			$this->assertAttributeSame(
				$restRequete,
				'restRequest',
				$restManager
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestSetRequeteNull() {
			$restManager = new RestManager();
			$restManager->setRequete(null);
		}

		public function testRestSetReponse() {
			$restReponse = $this->getMockRestReponse();
			$restManager = new RestManager();
			$restManager->setReponse($restReponse);

			$this->assertAttributeSame(
				$restReponse,
				'restResponse',
				$restManager
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestSetReponseNull() {
			$restManager = new RestManager();
			$restManager->setReponse(null);
		}

		public function testRestRecupererUriParam() {
			$restRequete = $this->createMock('RestRequete',
				array('getUriVariables', '', array('0' => 'monuri'))
			);

			$restManager = new RestManager();
			$restManager->setRequete($restRequete);

			$this->assertEquals('monuri', $restManager->getUriVariable(0));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestUriVariableNonInt() {
			$restManager = new RestManager();
			$restManager->getUriVariable('clef');
		}

		public function testRestUriVariableNull() {
			$restRequete = $this->createMock('RestRequete',
				array('getUriVariables', '', array('0' => 'monuri'))
			);

			$restManager = new RestManager();
			$restManager->setRequete($restRequete);

			$this->assertNull($restManager->getUriVariable(1));
		}

		public function testRestRecupererDonnee() {
			$restRequete = $this->createMock('RestRequete',
				array('getParametres', '', array('param1' => 'donnee1'))
			);

			$restManager = new RestManager();
			$restManager->setRequete($restRequete);

			$this->assertEquals('donnee1', $restManager->getParametre('param1'));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestRecupererDonneeClefNonString() {
			$restManager = new RestManager();
			$restManager->getParametre(50);
		}

		public function testRestRenvoieDonneeNull() {
			$restRequete = $this->createMock('RestRequete',
				array('getParametres', '', array('param1' => 'donnee1'))
			);

			$restManager = new RestManager();
			$restManager->setRequete($restRequete);

			$this->assertNull($restManager->getParametre('param2'));
		}

		public function testRestSetVariableReponse() {
			$restReponse = $this->createMock('RestReponse',
				array('setStatus', 500),
				array('setContenu', "<html></html>")
			);

			$restManager = new RestManager();
			$restManager->setReponse($restReponse);

			$restManager->setVariablesReponse(500, "<html></html>");
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

			$restManager = new RestManager();
			$restManager->setRequete($restRequete);
			$restManager->setReponse($restReponse);

			$this->assertEquals('{"param1":"var1"}', $restManager->fabriquerReponse(array('json')));
		}
	}