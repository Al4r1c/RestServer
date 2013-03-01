<?php
	namespace Tests\ServeurTests\Rest;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Rest\RestRequete;

	class RestRequeteTest extends TestCase {

		/**
		 * @var RestRequete $restRequete
		 */
		private $restRequete;

		protected function setUp() {
			$this->restRequete = new RestRequete();
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 20000
		 */
		public function testRestMethodeValide() {
			$this->restRequete->setMethode('METHODE_ERREUR');
		}

		public function testRestMethodeDefaultGet() {
			$this->assertEquals('GET', $this->restRequete->getMethode());
		}

		public function testRestMethodeAcceptePost() {
			$this->restRequete->setMethode('post');

			$this->assertEquals('POST', $this->restRequete->getMethode());
		}

		public function testRestAcceptFormatJSON() {
			$this->restRequete->setFormat('application/json');

			$this->assertContains('json', $this->restRequete->getFormatsDemandes());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestAcceptFormatInvalide() {
			$this->restRequete->setFormat(5);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 20001
		 */
		public function testRestFormatValide() {
			$this->restRequete->setFormat('HTTP_ACCEPT_INVALIDE');
		}

		public function testRestUri() {
			$this->restRequete->setVariableUri('/mon/uri/');

			$this->assertInternalType('array', $this->restRequete->getUriVariables());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestUriErronee() {
			$this->restRequete->setVariableUri(20.2);

			$this->assertInternalType('array', $this->restRequete->getUriVariables());
		}

		public function testRestUriVideNonGeree() {
			$this->restRequete->setVariableUri('/mon/uri/');
			$this->restRequete->setVariableUri('');

			$this->assertCount(2, $this->restRequete->getUriVariables());
		}

		public function testRestUriRecupererVariable() {
			$this->restRequete->setVariableUri('/variable1//var2////var3/');

			$this->assertEquals('variable1', $this->restRequete->getUriVariables()[0]);
			$this->assertEquals('var3', $this->restRequete->getUriVariables()[2]);
		}

		public function testRestRessourceEncode() {
			$this->restRequete->setVariableUri('/rés%s"ou#rce<////');

			$this->assertEquals(rawurlencode('rés%s"ou#rce<'), $this->restRequete->getUriVariables()[0]);
		}

		public function testRestDonnee() {
			$this->restRequete->setParametres(array());

			$this->assertInternalType('array', $this->restRequete->getParametres());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestDonneeSeulementTableau() {
			$this->restRequete->setParametres('GO_GO_ERREUR');
		}

		public function testParametreSauvegardes() {
			$this->restRequete->setParametres(array("param1" => "valeur1", "data" => 1));
			$this->assertCount(2, $this->restRequete->getParametres());
		}

		public function testRecupererParametre() {
			$this->restRequete->setParametres(array("param1" => "valeur1", "data" => 1));
			$this->assertEquals('valeur1', $this->restRequete->getParametres()['param1']);
		}

		public function testRestSetServer() {
			$serveur = $this->createMock('Server',
				array('getServeurMethode', '', 'PUT'),
				array('getServeurHttpAccept', '', 'text/html,application/xhtml+xml,application/xml;q=0.9'),
				array('getServeurUri', '', '/mon/uri/'),
				array('getServeurDonnees', '', array('param1' => 'var1', 'param2' => 'var2'))
			);

			$this->restRequete->setServer($serveur);
			$this->assertEquals('PUT', $this->restRequete->getMethode());
			$this->assertContains('xml', $this->restRequete->getFormatsDemandes());
			$this->assertEquals('uri', $this->restRequete->getUriVariables()[1]);
			$this->assertEquals('var2', $this->restRequete->getParametres()['param2']);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testRestSetServerEronnee() {
			$this->restRequete->setServer(null);
		}
	}