<?php
	namespace Tests\I18n;

	include_once(__DIR__ . '/../../TestEnv.php');

	use Tests\TestCase;

	class I18nManagerTest extends TestCase {

		public function testSetLangueDefaut() {
			$i18nManager = new \Serveur\I18n\I18nManager();
			$i18nManager->setLangueDefaut('Mexicain');

			$this->assertAttributeEquals(
				'Mexicain',
				'langueDefaut',
				$i18nManager
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\I18nManagerException
		 * @expectedExceptionCode 40000
		 */
		public function testSetLangueDefautErreur() {
			$i18nManager = new \Serveur\I18n\I18nManager();
			$i18nManager->setLangueDefaut(null);
		}

		public function testSetLangueDispo() {
			$i18nManager = new \Serveur\I18n\I18nManager();
			$i18nManager->setLangueDispo(array('Allemand' => 'al', 'Kosovar' => 'ksv'));

			$this->assertAttributeEquals(
				array('Allemand' => 'al', 'Kosovar' => 'ksv'),
				'languesDisponibles',
				$i18nManager
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\I18nManagerException
		 * @expectedExceptionCode 40001
		 */
		public function testSetLangueDispoErreur() {
			$i18nManager = new \Serveur\I18n\I18nManager();
			$i18nManager->setLangueDispo(array());
		}

		public function testSetConfig() {
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_lang', 'French'),
				array('getConfigValeur', 'languages', array('French' => 'fr', 'English' => 'en'))
			);

			$i18nManager = new \Serveur\I18n\I18nManager();
			$i18nManager->setConfig($config);

			$this->assertAttributeEquals(
				'French',
				'langueDefaut',
				$i18nManager
			);

			$this->assertAttributeEquals(
				array('French' => 'fr', 'English' => 'en'),
				'languesDisponibles',
				$i18nManager
			);
		}

		public function testGetFichierTraduction() {
			/** @var $config \Serveur\Config\Config */
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_lang', 'French'),
				array('getConfigValeur', 'languages', array('FRENCH' => 'fr', 'ENGLISH' => 'en'))
			);

			$xmlParser = $this->createMock('xmlparser',
				array('isValide', '', true)
			);

			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParser)
			);

			/** @var $i18nManager \Serveur\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'fr', $fichier)
			);

			$i18nManager->setConfig($config);

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionDefautInexistant() {
			/** @var $config \Serveur\Config\Config */
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_lang', 'French'),
				array('getConfigValeur', 'languages', array('ENGLISH' => 'en'))
			);

			$xmlParser = $this->createMock('xmlparser',
				array('isValide', '', true)
			);

			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParser)
			);

			/** @var $i18nManager \Serveur\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'en', $fichier)
			);

			$i18nManager->setConfig($config);

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionDefautInvalide() {
			/** @var $config \Serveur\Config\Config */
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_lang', 'French'),
				array('getConfigValeur', 'languages', array('CHINOIS' => 'cn', 'FRENCH' => 'fr'))
			);

			$xmlParserCn = $this->createMock('xmlparser',
				array('isValide', '', true)
			);

			$xmlParserFr = $this->createMock('xmlparser',
				array('isValide', '', false)
			);

			$fichierCn = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParserCn)
			);

			$fichierFr = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParserFr)
			);

			/** @var $i18nManager \Serveur\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'fr', $fichierFr),
				array('getFichier', 'cn', $fichierCn)
			);

			$i18nManager->setConfig($config);

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionPlusieursFichierInexistantOuBugges() {
			/** @var $config \Serveur\Config\Config */
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_lang', 'English'),
				array('getConfigValeur', 'languages', array('ENGLISH' => 'en', 'ITALIAN' => 'it', 'FRENCH' => 'fr'))
			);

			$xmlParserIt = $this->createMock('xmlparser',
				array('isValide', '', false)
			);

			$xmlParserFr = $this->createMock('xmlparser',
				array('isValide', '', true)
			);

			$fichierEn = $this->createMock('Fichier',
				array('fichierExiste', '', false)
			);

			$fichierIt = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParserIt)
			);

			$fichierFr = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParserFr)
			);

			/** @var $i18nManager \Serveur\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'en', $fichierEn),
				array('getFichier', 'en', $fichierEn),
				array('getFichier', 'it', $fichierIt),
				array('getFichier', 'fr', $fichierFr)
			);

			$i18nManager->setConfig($config);

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionAucunFichierFonctionnel() {
			/** @var $config \Serveur\Config\Config */
			$config = $this->createMock('Config',
				array('getConfigValeur', 'config.default_lang', 'English'),
				array('getConfigValeur', 'languages', array('ENGLISH' => 'en'))
			);

			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', false)
			);

			/** @var $i18nManager \Serveur\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', '', $fichier)
			);

			$i18nManager->setConfig($config);

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}
	}