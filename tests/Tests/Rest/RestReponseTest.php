<?php
	namespace Tests\Rest;

	include_once(__DIR__ . '/../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Rest\RestReponse;

	class RestReponseTest extends TestCase {

		/**
		 * @var RestReponse $restRequete
		 */
		private $restReponse;

		protected function setUp() {
			$this->restReponse = new RestReponse();
		}

		/**
		 * @expectedException     \PHPUnit_Framework_Error
		 */
		public function testRestSetHeaderManager() {
			$this->restReponse->setHeaderManager(null);
		}

		public function testRestContenu() {
			$this->restReponse->setContenu(array('param' => 'variable', 'param2' => 'var2'));
			$this->assertCount(2, $this->restReponse->getContenu());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20100
		 */
		public function testRestContenuArray() {
			$this->restReponse->setContenu('INVALID');
		}

		public function testRestStatus() {
			$this->restReponse->setStatus(200);
			$this->assertEquals(200, $this->restReponse->getStatus());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20101
		 */
		public function testRestStatusValide() {
			$this->restReponse->setStatus(999);
		}

		public function testRestSetFormatDefaut() {
			$this->restReponse->setFormatRetourDefaut('JSON');
			$this->assertEquals('JSON', $this->restReponse->getFormatRetourDefaut());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20103
		 */
		public function testRestSetFormatDefautInvalide() {
			$this->restReponse->setFormatRetourDefaut(array());
		}

		public function testRestSetFormatAcceptes() {
			$this->restReponse->setFormatsAcceptes(array('JSON' => 'json', 'TEXT' => 'txt'));
			$this->assertCount(2, $this->restReponse->getFormatsAcceptes());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20104
		 */
		public function testRestSetFormatAcceptesInvalid() {
			$this->restReponse->setFormatsAcceptes('ERROR');
		}

		public function testRestSetFormat() {
			$this->restReponse->setFormats('PLAIN', array('PLAIN' => 'txt'));
			$this->assertEquals('PLAIN', $this->restReponse->getFormatRetourDefaut());
			$this->assertCount(1, $this->restReponse->getFormatsAcceptes());
		}

		public function testRestSetFormatDefautInexistant() {
			$this->restReponse->setFormats('PLAIN', array('HTML' => 'html'));
			$this->assertEquals('HTML', $this->restReponse->getFormatRetourDefaut());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20104
		 */
		public function testRestFormatDefaut() {
			$this->restReponse->setFormats('PLAIN', array());
		}

		public function testRestSetCharset() {
			$this->restReponse->setCharset('utf-8');
			$this->assertEquals('utf-8', $this->restReponse->getCharset());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20105
		 */
		public function testRestSetCharsetInvalid() {
			$this->restReponse->setCharset('UTF-9999999999');
		}

		public function testRestSetConfig() {
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_render', 'JSON'),
				array('getConfigValeur', 'render', array('JSON' => 'json', 'HTML' => 'html')),
				array('getConfigValeur', 'config.charset', 'utf-8')
			);

			$this->restReponse->setConfig($config);
			$this->assertEquals('utf-8', $this->restReponse->getCharset());
			$this->assertEquals('JSON', $this->restReponse->getFormatRetourDefaut());
			$this->assertCount(2, $this->restReponse->getFormatsAcceptes());
		}

		public function testRestRenderAvecExempleJson() {
			$headerManager = $this->createMock('HeaderManager',
				array('ajouterHeader'),
				array('envoyerHeaders')
			);

			$this->restReponse->setHeaderManager($headerManager);

			$this->restReponse->setContenu(array('param1' => 'var1'));
			$this->restReponse->setFormats('JSON', array('JSON' => 'json'));

			$this->assertEquals('{"param1":"var1"}', $this->restReponse->fabriquerReponse(array('json')));
		}

		public function testRestRenderAbstract() {
			$abstractrender = $this->createMock('AbstractRenderer',
				array('render', array('getKey' => 'getVar'), '{"getKey":"getVar"}')
			);

			/** @var $restReponse RestReponse|\PHPUnit_Framework_MockObject_MockObject */
			$restReponse = $this->createMock('RestReponse',
				array('getRenderClass', 'Json', $abstractrender)
			);

			$headerManager = $this->createMock('HeaderManager',
				array('ajouterHeader'),
				array('envoyerHeaders')
			);

			$restReponse->setHeaderManager($headerManager);
			$restReponse->setContenu(array('getKey' => 'getVar'));
			$restReponse->setFormats('JSON', array('JSON' => 'json'));

			$this->assertEquals('{"getKey":"getVar"}', $restReponse->fabriquerReponse(array('json')));
		}

		public function testRestRenderNonTrouveUtiliseAutre() {
			$abstractrender = $this->createMock('AbstractRenderer',
				array('render', array('param1' => 'var1'), '{"param1":"var1"}')
			);

			/** @var $restReponse RestReponse|\PHPUnit_Framework_MockObject_MockObject */
			$restReponse = $this->createMock('RestReponse',
				array('getRenderClass', 'Json', $abstractrender)
			);

			$headerManager = $this->createMock('HeaderManager',
				array('ajouterHeader'),
				array('envoyerHeaders')
			);

			$restReponse->setHeaderManager($headerManager);
			$restReponse->setContenu(array('param1' => 'var1'));
			$restReponse->setFormats('JSON', array('JSON' => 'json'));

			$this->assertEquals('{"param1":"var1"}', $restReponse->fabriquerReponse(array('fake')));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20106
		 */
		public function testRestRenderNonTrouveDefautNonPlus() {
			$this->restReponse->setFormatRetourDefaut('HTML');
			$this->restReponse->setFormatsAcceptes(array('JSON' => 'json'));

			$this->restReponse->fabriquerReponse(array('fake'));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\RestReponseException
		 * @expectedExceptionCode 20107
		 */
		public function testRestRenderNonTrouve() {
			$this->restReponse->setFormats('FAKE', array('FAKE' => 'fake'));

			$this->restReponse->fabriquerReponse(array('fake'));
		}
	}