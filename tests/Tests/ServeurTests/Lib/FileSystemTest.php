<?php
	namespace Tests\ServeurTests\Lib;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Lib\FileSystem;
	use org\bovigo\vfs\vfsStreamWrapper;
	use org\bovigo\vfs\vfsStream;

	class FileSystemTest extends TestCase {

		/** @var FileSystem */
		private $fileSystem;

		public function setUp() {
			$this->fileSystem = new FileSystem();
		}

		private function activerFakeFileSystem() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));
			$this->fileSystem->setBasePath(vfsStream::url('testPath'));
		}

		public function testSetOs() {
			foreach(array('Windows', 'Mac', 'Linux', 'FreeBSD') as $unOsValide) {
				$this->fileSystem->setOs($unOsValide);

				$this->assertAttributeEquals(
					$unOsValide,
					'os',
					$this->fileSystem
				);
			}
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 10100
		 */
		public function testSetWrongOs() {
			$this->fileSystem->setOs('Negatif');
		}

		public function testSetBasePath() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));
			$this->fileSystem->setBasePath(vfsStream::url('testPath'));

			$this->assertAttributeEquals(
				vfsStream::url('testPath'),
				'basePath',
				$this->fileSystem
			);
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 10101
		 */
		public function testSetBasePathWrong() {
			$this->fileSystem->setOs('Windows');
			$this->fileSystem->setBasePath('/my/base/path/');
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 10102
		 */
		public function testSetBasePathInexistant() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));
			$this->fileSystem->setBasePath(vfsStream::url('testPath/wrong/'));
		}

		public function testFichierExiste() {
			$this->activerFakeFileSystem();

			$this->assertFalse($this->fileSystem->fichierExiste(vfsStream::url('testPath/fichier.fake')));

			file_put_contents(vfsStream::url('testPath/fichier.fake'), 'Contenu');

			$this->assertTrue($this->fileSystem->fichierExiste(vfsStream::url('testPath/fichier.fake')));
		}

		public function testDossierExiste() {
			$this->activerFakeFileSystem();

			$this->assertFalse($this->fileSystem->dossierExiste(vfsStream::url('testPath/newDossier')));

			mkdir(vfsStream::url('testPath/newDossier/'));

			$this->assertTrue($this->fileSystem->dossierExiste(vfsStream::url('testPath/newDossier')));
		}

		public function testGetExtension() {
			$this->assertEquals('jpeg', $this->fileSystem->getExtension('unFichier.jpeg'));
		}

		public function testGetDroits() {
			$this->activerFakeFileSystem();

			file_put_contents(vfsStream::url('testPath/page.html'), 'Contenu');

			chmod(vfsStream::url('testPath/page.html'), 0174);

			$this->assertEquals('0174', $this->fileSystem->getDroits(vfsStream::url('testPath/page.html')));
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 10103
		 */
		public function testGetDroitsFichierInexistant() {
			$this->fileSystem->getDroits('isAFAKE.html');
		}

		public function testCreerFichier() {
			$this->activerFakeFileSystem();

			$this->assertFalse($this->fileSystem->fichierExiste(vfsStream::url('testPath/nouveauFichier.fake')));

			$this->fileSystem->creerFichier(vfsStream::url('testPath/nouveauFichier.fake'));

			$this->assertTrue($this->fileSystem->fichierExiste(vfsStream::url('testPath/nouveauFichier.fake')));
		}

		public function testCreerFichierProbleme() {
			$this->activerFakeFileSystem();

			mkdir(vfsStream::url('testPath/path/'));
			chmod(vfsStream::url('testPath/path/'), '0000');

			$this->assertFalse($this->fileSystem->creerFichier(vfsStream::url('testPath/path/nouveauFichier.fake')));
		}

		public function testChargerFichier() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));

			$abstractChargeur = $this->createMock('AbstractChargeurFichier', array('chargerFichier', vfsStream::url('testPath/fichier.php'), array('paris' => 'yeah')));

			/** @var $fileSystem FileSystem */
			$fileSystem = $this->createMock('FileSystem', array('getChargeurClass', 'Php', $abstractChargeur));

			$fileSystem->setBasePath(vfsStream::url('testPath'));
			$fileSystem->creerFichier(vfsStream::url('testPath/fichier.php'));

			$this->assertEquals(array('paris' => 'yeah'), $fileSystem->chargerFichier(vfsStream::url('testPath/fichier.php')));
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 10105
		 */
		public function testChargerFichierInexistant() {
			$this->activerFakeFileSystem();

			$this->fileSystem->chargerFichier(vfsStream::url('testPath/fichier.php'));
		}

		/**
		 * @expectedException     \Exception
		 * @expectedExceptionCode 10106
		 */
		public function testChargerFichierChargeurNonPresent() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));

			$this->fileSystem->setBasePath(vfsStream::url('testPath'));
			$this->fileSystem->creerFichier(vfsStream::url('testPath/fichier.xodkeispt99'));

			$this->fileSystem->chargerFichier(vfsStream::url('testPath/fichier.xodkeispt99'));
		}

		public function testRelatifToAbsoluNormal() {
			$this->assertEquals('/home/ok/', $this->fileSystem->relatifToAbsolu('/home/ok/'));
		}

		public function testRelatifToAbsoluPoint() {
			$this->assertEquals('/home/cheminsupp/ok/', $this->fileSystem->relatifToAbsolu('/home//cheminsupp/./ok/'));
		}

		public function testRelatifToAbsoluDoublePoints() {
			$this->assertEquals('/home/ok/', $this->fileSystem->relatifToAbsolu('/home//cheminsupp/../ok/'));
		}

		public function testGetDirectorySeparateurWindows() {
			$this->fileSystem->setOs('Windows');

			$this->assertEquals('\\', $this->fileSystem->getDirectorySeparateur());
		}

		public function testGetDirectorySeparateurAutreOS() {
			$this->fileSystem->setOs('Mac');

			$this->assertEquals('/', $this->fileSystem->getDirectorySeparateur());
		}

		public function testIsAbsoluteWindows() {
			$this->fileSystem->setOs('Windows');

			$class = new \ReflectionClass('Serveur\Lib\FileSystem');
			$method = $class->getMethod('isAbsolutePath');
			$method->setAccessible(true);

			$this->assertTrue($method->invokeArgs($this->fileSystem, array('c:\home/')));
			$this->assertTrue($method->invokeArgs($this->fileSystem, array('\\\\serveur\\chemin\\')));
			$this->assertTrue($method->invokeArgs($this->fileSystem, array('stream://streamChemin/')));

			$this->assertFalse($method->invokeArgs($this->fileSystem, array('/home/')));
			$this->assertFalse($method->invokeArgs($this->fileSystem, array('home/')));
		}

		public function testIsAbsoluteAutresOS() {
			$this->fileSystem->setOs('Linux');

			$class = new \ReflectionClass('Serveur\Lib\FileSystem');
			$method = $class->getMethod('isAbsolutePath');
			$method->setAccessible(true);

			$this->assertTrue($method->invokeArgs($this->fileSystem, array('/home/')));
			$this->assertTrue($method->invokeArgs($this->fileSystem, array('stream://streamChemin/')));

			$this->assertFalse($method->invokeArgs($this->fileSystem, array('home/')));
			$this->assertFalse($method->invokeArgs($this->fileSystem, array('c:\home/')));
			$this->assertFalse($method->invokeArgs($this->fileSystem, array('\\\\serveur\\chemin\\')));
		}
	}