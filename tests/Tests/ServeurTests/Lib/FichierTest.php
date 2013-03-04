<?php
	namespace Tests\ServeurTests\Lib;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Lib\Fichier;

	class FichierTest extends TestCase {

		/** @var Fichier */
		private $fichier;

		public function setUp() {
			$this->fichier = \Serveur\Utils\FileManager::getFichier();
		}

		public function testFileSystem() {
			$fileSystem = $this->getMockFileSystem();

			$this->fichier->setFileSystem($fileSystem);

			$this->assertEquals($fileSystem, $this->fichier->getFileSystem());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testSetNotFileSystem() {
			$this->fichier->setFileSystem('should be object');
		}

		public function testNomFichier() {
			$this->fichier->setNomFichier('monFichier.txt');

			$this->assertEquals('monFichier.txt', $this->fichier->getNomFichier());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testNomFichierNotString() {
			$this->fichier->setNomFichier(5);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\MainException
		 * @expectedExceptionCode 10200
		 */
		public function testNomFichierNonNull() {
			$this->fichier->setNomFichier(' ');
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\MainException
		 * @expectedExceptionCode 10201
		 */
		public function testNomFichierInvalid() {
			$this->fichier->setNomFichier('monFichierSansExtension');
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @expectedExceptionCode 1000
		 */
		public function testCheminDaccesNonString() {
			$this->fichier->setRepertoireFichier(23.3);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\MainException
		 * @expectedExceptionCode 10202
		 */
		public function testCheminDaccesNull() {
			$this->fichier->setRepertoireFichier(' ');
		}

		public function testCheminDacces() {
			$this->fichier->setRepertoireFichier('/path\\\\to///fichier////');

			$this->assertEquals(BASE_PATH . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR . 'fichier' . DIRECTORY_SEPARATOR, $this->fichier->getRepertoireFichier());
		}

		public function testCheminDaccesAbsolue() {
			$this->fichier->setRepertoireFichier(BASE_PATH . '/path/to/fichier/');

			$this->assertEquals(BASE_PATH . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR . 'fichier' . DIRECTORY_SEPARATOR, $this->fichier->getRepertoireFichier());
		}

		public function testGetCheminComplet() {
			$this->fichier->setRepertoireFichier('/path/');
			$this->fichier->setNomFichier('comeatme.log');

			$this->assertEquals(BASE_PATH . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'comeatme.log', $this->fichier->getCheminCompletFichier());
		}

		public function testSetFichierConfig() {
			$this->fichier->setFichierParametres('monFichier.txt', '/chemin\\dacces/');

			$this->assertEquals(BASE_PATH . DIRECTORY_SEPARATOR . 'chemin' . DIRECTORY_SEPARATOR . 'dacces' . DIRECTORY_SEPARATOR . 'monFichier.txt', $this->fichier->getCheminCompletFichier());
		}

		public function testVerifierExistence() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', 'c:\\wwww\\', true),
				array('fichierExiste', 'c:\\wwww\\chemin\\monFichier.png', true)
			);

			$fileSystem->initialiser('Windows', 'c:\\wwww\\');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('monFichier.png', '/chemin/');

			$this->assertTrue($this->fichier->fichierExiste());
		}

		public function testVerifierExistenceFalse() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', 'c:\\wwww\\', true),
				array('fichierExiste', 'c:\\wwww\\chemin\\monFichier.png', false)
			);

			$fileSystem->initialiser('Windows', 'c:\\wwww\\');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('monFichier.png', '/chemin/');

			$this->assertFalse($this->fichier->fichierExiste());
		}

		public function testChargerFichier() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', '/data/www/', true),
				array('fichierExiste', '/data/www/chemin/variables.php', true),
				array('chargerFichier', '/data/www/chemin/variables.php', array('VAR1' => 'PARAM1'))
			);

			$fileSystem->initialiser('Linux', '/data/www/');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('variables.php', '/chemin/');

			$this->assertEquals(array('VAR1' => 'PARAM1'), $this->fichier->chargerFichier());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\MainException
		 * @expectedExceptionCode 10203
		 */
		public function testChargerFichierInexistant() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', '/data/www/', true),
				array('fichierExiste', '/data/www/chemin/monFichierFAke.php', false)
			);

			$fileSystem->initialiser('Linux', '/data/www/');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('monFichierFAke.php', '/chemin/');

			$this->fichier->chargerFichier();
		}

		public function testCreerFichier() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', '', true),
				array('creerFichier', '/data/www/chemin/party.png', true)
			);

			$fileSystem->initialiser('Linux', '/data/www/');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('party.png', 'chemin/');

			$this->assertTrue($this->fichier->creerFichier());
		}

		public function testNeRecreerPasFichierExisteDeja() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', '', true),
				array('fichierExiste', '/data/www/chemin/party.png', true)
			);

			$fileSystem->initialiser('Linux', '/data/www/');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('party.png', 'chemin/');

			$this->assertTrue($this->fichier->creerFichier());
		}


		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\MainException
		 * @expectedExceptionCode 10204
		 */
		public function testCreerDansDossierInexistant() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', 'c:\\www\\', true),
				array('dossierExiste', 'c:\\www\\path\\', false)
			);

			$fileSystem->initialiser('Windows', 'c:\\www\\');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('party.png', '/path/');

			$this->fichier->creerFichier();
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\MainException
		 * @expectedExceptionCode 10205
		 */
		public function testCreerBug() {
			/** @var $fileSystem \Serveur\Lib\FileSystem */
			$fileSystem = $this->createMock('FileSystem',
				array('dossierExiste', '', true),
				array('creerFichier', 'c:\\www\\path\\heya.log', '')
			);

			$fileSystem->initialiser('Windows', 'c:\\www\\');

			$this->fichier->setFileSystem($fileSystem);

			$this->fichier->setFichierParametres('heya.log', '/path/');

			$this->fichier->creerFichier();
		}
	}