<?php
	namespace Tests\LoggingTests\I18n;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;

	class I18nManagerTest extends TestCase {

		public function testSetLangueDefaut() {
			$i18nManager = new \Logging\I18n\I18nManager();
			$i18nManager->setLangueDefaut('Mexicain');

			$this->assertAttributeEquals(
				'Mexicain',
				'langueDefaut',
				$i18nManager
			);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionMessage Default language is not set properly.
		 */
		public function testSetLangueDefautErreur() {
			$i18nManager = new \Logging\I18n\I18nManager();
			$i18nManager->setLangueDefaut(null);
		}

		public function testSetLangueDispo() {
			$i18nManager = new \Logging\I18n\I18nManager();
			$i18nManager->setLangueDispo(array('Allemand' => 'al', 'Kosovar' => 'ksv'));

			$this->assertAttributeEquals(
				array('Allemand' => 'al', 'Kosovar' => 'ksv'),
				'languesDisponibles',
				$i18nManager
			);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionMessage No available language set.
		 */
		public function testSetLangueDispoErreur() {
			$i18nManager = new \Logging\I18n\I18nManager();
			$i18nManager->setLangueDispo(array());
		}

		public function testSetConfig() {
			$i18nManager = new \Logging\I18n\I18nManager();
			$i18nManager->setConfig('French', array('French' => 'fr', 'English' => 'en'));

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
			$xmlParser = $this->createMock('xmlparser',
				array('isValide', '', true)
			);

			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParser)
			);

			/** @var $i18nManager \Logging\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'fr', $fichier)
			);

			$i18nManager->setConfig('French', array('FRENCH' => 'fr', 'ENGLISH' => 'en'));

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionDefautInexistant() {
			$xmlParser = $this->createMock('xmlparser',
				array('isValide', '', true)
			);

			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('chargerFichier', '', $xmlParser)
			);

			/** @var $i18nManager \Logging\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'en', $fichier)
			);

			$i18nManager->setConfig('French', array('ENGLISH' => 'en'));

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionDefautInvalide() {
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

			/** @var $i18nManager \Logging\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'fr', $fichierFr),
				array('getFichier', 'cn', $fichierCn)
			);

			$i18nManager->setConfig('French', array('CHINOIS' => 'cn', 'FRENCH' => 'fr'));

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testGetFichierTraductionPlusieursFichierInexistantOuBugges() {
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

			/** @var $i18nManager \Logging\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', 'en', $fichierEn),
				array('getFichier', 'en', $fichierEn),
				array('getFichier', 'it', $fichierIt),
				array('getFichier', 'fr', $fichierFr)
			);

			$i18nManager->setConfig('English', array('ENGLISH' => 'en', 'ITALIAN' => 'it', 'FRENCH' => 'fr'));

			$this->assertThat(
				$i18nManager->getFichierTraduction(),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionMessage No valid translation file set or found.
		 */
		public function testGetFichierTraductionAucunFichierFonctionnel() {
			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', false)
			);

			/** @var $i18nManager \Logging\I18n\I18nManager */
			$i18nManager = $this->createMock('I18nManager',
				array('getFichier', '', $fichier)
			);

			$i18nManager->setConfig('English', array('ENGLISH' => 'en'));

			$i18nManager->getFichierTraduction();
		}
	}