<?php
	namespace Tests\Config;

	include_once(__DIR__ . '/../../TestEnv.php');

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
				'DEFAULT_LANG' => 'French',
				'DEFAULT_RENDER' => 'XML'
			),
			'Displayers' => array(
				'LOG' =>  'logger'
			),
			'Render' => array (
				'XML' =>  'xml'
			),
			'Languages' => array (
				'FRENCH' =>  'fr',
				'ENGLISH' =>  'en'
			)
		);

		public function setUp() {
			$this->configuration = new Config();
		}

		public function testChargerFichier() {
			$fichier = $this->createMock('Fichier',
				array('existe','', true),
				array('charger','', self::$donneesConfig)
			);

			$this->configuration->chargerConfiguration($fichier);

			$this->assertAttributeEquals(
				array_change_key_case(self::$donneesConfig, CASE_UPPER),
				'applicationConfiguration',
				$this->configuration
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ConfigException
		 * @expectedExceptionCode 30000
		 */
		public function testChargerFichierInexistant() {
			$fichier = $this->createMock('Fichier',
				array('existe','', false)
			);

			$this->configuration->chargerConfiguration($fichier);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ConfigException
		 * @expectedExceptionCode 30001
		 */
		public function testChargerFichierInvalide() {
			$donnees = self::$donneesConfig;
			unset($donnees['Languages']);
			$fichier = $this->createMock('Fichier',
				array('existe','', true),
				array('charger','', $donnees)
			);

			$this->configuration->chargerConfiguration($fichier);
		}

		public function testGetValeur() {
			$fichier = $this->createMock('Fichier',
				array('existe','', true),
				array('charger','', self::$donneesConfig)
			);

			$this->configuration->chargerConfiguration($fichier);

			$this->assertEquals('LOG', $this->configuration->getConfigValeur('config.default_displayer'));
			$this->assertEquals('xml', $this->configuration->getConfigValeur('render.xml'));
			$this->assertNull($this->configuration->getConfigValeur('render.existepas'));
		}
	}