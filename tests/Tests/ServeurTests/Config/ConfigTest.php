<?php
	namespace Tests\ServeurTests\Config;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Config\Config;

	class ConfigTest extends TestCase {
		/** @var Config */
		private $configuration;
		private static $donneesConfig = array(
			'Config' => array (
				'DEBUG_WEBSITE' => true,
				'DEBUG_FRAMEWORK' => true,
				'CHARSET' => 'utf-8',
				'DEFAULT_DISPLAYER' => 'LOG',
				'DEFAULT_RENDER' => 'XML'
			),
			'Displayers' => array(
				'LOG' =>  'logger'
			),
			'Render' => array (
				'XML' =>  'xml'
			)
		);

		public function setUp() {
			$this->configuration = new Config();
		}

		public function testChargerFichier() {
			/** @var $fichier \Serveur\Lib\Fichier */
			$fichier = $this->createMock('Fichier',
				array('chargerFichier', '', self::$donneesConfig)
			);

			$this->configuration->chargerConfiguration($fichier);

			$this->assertAttributeEquals(
				array_change_key_case(self::$donneesConfig, CASE_UPPER),
				'applicationConfiguration',
				$this->configuration
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testChargerConfigurationArgumentDoitEtreFichier() {
			$this->configuration->chargerConfiguration(null);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 30000
		 */
		public function testChargerFichierInexistant() {
			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', false)
			);

			$this->configuration->chargerConfiguration($fichier);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 30001
		 */
		public function testChargerFichierInvalide() {
			$donnees = self::$donneesConfig;
			unset($donnees['Displayers']);
			$fichier = $this->createMock('Fichier',
				array('chargerFichier','', $donnees)
			);

			$this->configuration->chargerConfiguration($fichier);
		}

		public function testGetValeur() {
			$fichier = $this->createMock('Fichier',
				array('chargerFichier','', self::$donneesConfig)
			);

			$this->configuration->chargerConfiguration($fichier);

			$this->assertEquals('LOG', $this->configuration->getConfigValeur('config.default_displayer'));
			$this->assertEquals('xml', $this->configuration->getConfigValeur('render.xml'));
			$this->assertNull($this->configuration->getConfigValeur('render.existepas'));
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testGetValeurNonString() {
			$this->configuration->getConfigValeur(3);
		}
	}