<?php
	namespace Modules\ServeurTests\Lib;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Modules\TestCase;
	use Serveur\Lib\TypeDetector;

	class TypeDetectorTest extends TestCase {

		/** @var $typeDetector TypeDetector */
		private $typeDetector;

		private static $mimeFichier = array (
			'bmp' => 'image/bmp',
			'css' => 'text/css',
			'csv' => 'text/x-comma-separated-values',
			'html' => 'text/html',
			'jpg' => 'image/jpeg',
			'js' => 'application/javascript',
			'php' => 'application/x-httpd-php',
			'xhtml' => 'application/xhtml+xml',
			'xml' => 'application/xml',
		);

		public function setUp() {
			/** @var $constantes \Serveur\Utils\Constante * */
			$constantes = $this->createMock('Constante',
				array('chargerConfig', 'mimes', self::$mimeFichier)
			);

			$this->typeDetector = new TypeDetector($constantes::chargerConfig('mimes'));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testNewTypeDetectorArgumentNonArray() {
			new TypeDetector(null);
		}

		public function testRecupererMimeTypeViaExtension() {
			$this->assertEquals('application/javascript', $this->typeDetector->getMimeType('js'));
		}

		public function testRecupererMimeTypeInexistant() {
			$this->assertEquals('*/*', $this->typeDetector->getMimeType('fake'));
		}

		public function testExtraireMimesTypeHeader() {
			$this->assertEquals(array('css'), $this->typeDetector->extraireMimesTypeHeader('text/css'));
		}

		public function testExtraireMimesGeneric() {
			$this->assertEmpty($this->typeDetector->extraireMimesTypeHeader('*/*'));
		}

		public function testExtraireMimesTypeNonTrouve() {
			$this->assertEmpty($this->typeDetector->extraireMimesTypeHeader('fake'));
		}

		public function testExtraiteMimesQualite() {
			$this->assertEquals(array('html', 'xhtml', 'xml'), $this->typeDetector->extraireMimesTypeHeader('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'));
		}
	}