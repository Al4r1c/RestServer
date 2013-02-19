<?php
	namespace Tests\Rest;

	include_once(__DIR__ . '/../../TestEnv.php');

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
		 * @expectedException     \PHPUnit_Framework_Error
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
		 * @expectedException     \PHPUnit_Framework_Error
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

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20101
		 */
		public function testRestSetVariableReponseErreur() {
			$restManager = new RestManager();
			$restManager->setReponse(new \Serveur\Rest\RestReponse());

			$restManager->setVariablesReponse(900);
		}

		public function testRestFabriquerReponse() {
			$restRequete = $this->createMock('RestRequete',
				array('getFormatsDemandes', '', array('json'))
			);

			$restReponse = new \Serveur\Rest\RestReponse();
			$restReponse->setContenu(array('param1' => 'var1'));
			$restReponse->setFormats('JSON', array('JSON' => 'json'));

			$restManager = new RestManager();
			$restManager->setRequete($restRequete);
			$restManager->setReponse($restReponse);

			$this->assertEquals('{"param1":"var1"}', $restManager->fabriquerReponse(array('json')));
		}
	}