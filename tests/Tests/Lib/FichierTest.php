<?php
	namespace Tests\Lib;

	include_once(__DIR__ . '/../../TestEnv.php');

	use Tests\TestCase;
	use Serveur\Lib\Fichier;
	use org\bovigo\vfs\vfsStreamWrapper;
	use org\bovigo\vfs\vfsStream;

	class FichierTest extends TestCase {

		/** @var Fichier */
		private $fichier;

		public function setUp() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));
			$this->fichier = new Fichier();
			$this->fichier->setBasePath(vfsStream::url('testPath'));
		}

		public function testBasePath() {
			$this->fichier->setBasePath(vfsStream::url('testPath'));
			$this->assertAttributeEquals(
				vfsStream::url('testPath'),
				'basePath',
				$this->fichier
			);
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10100
		 */
		public function testBasePathVide() {
			$this->fichier->setBasePath('');
		}

		public function testNomFichier() {
			$this->fichier->setNom('monFichier.txt');

			$this->assertEquals('monFichier.txt', $this->fichier->getNom());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10101
		 */
		public function testNomFichierNonNull() {
			$this->fichier->setNom(' ');
		}

		public function testGetExtension() {
			$this->fichier->setNom('monFichier.mOnExT');

			$this->assertEquals(strtolower('mOnExT'), $this->fichier->getExtension());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10102
		 */
		public function testNomFichierInvalid() {
			$this->fichier->setNom('monFichierSansExtension');
		}

		public function testCheminDaccesNull() {
			$this->fichier->setCheminAcces(null);
			$this->assertEquals(vfsStream::url('testPath/'), $this->fichier->getCheminAcces());
		}

		public function testCheminDacces() {
			$this->fichier->setCheminAcces('path/to/fichier/', true);

			$this->assertEquals(vfsStream::url('testPath/path/to/fichier'), $this->fichier->getCheminAcces());
		}

		public function testCheminDaccesAbsolue() {
			$this->fichier->setCheminAcces('/path/to/fichier/', false);

			$this->assertEquals('/path/to/fichier', $this->fichier->getCheminAcces());
		}

		public function testGetLocationFichier() {
			$this->fichier->setCheminAcces('/path/to/fichier/', false);
			$this->fichier->setNom('comeatme.log');

			$this->assertEquals('/path/to/fichier/comeatme.log', $this->fichier->getLocationFichier());
		}

		public function testSetFichierConfig() {
			$this->fichier->setFichierConfig('monFichier.txt', '/chemin/daccess/');

			$this->assertEquals('monFichier.txt', $this->fichier->getNom());
			$this->assertEquals(strtolower('txt'), $this->fichier->getExtension());
			$this->assertEquals(vfsStream::url('testPath/chemin/daccess'), $this->fichier->getCheminAcces());
			$this->assertEquals(vfsStream::url('testPath/chemin/daccess/monFichier.txt'), $this->fichier->getLocationFichier());
		}

		public function testGetDroits() {
			file_put_contents(vfsStream::url('testPath/page.html'), 'some content');
			chmod(vfsStream::url('testPath/page.html'), 0124);

			$this->fichier->setFichierConfig('page.html', '');

			$this->assertEquals('0124', $this->fichier->getDroits());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10103
		 */
		public function testGetDroitsFichierInexistant() {
			$this->fichier->setFichierConfig('page.html', '');

			$this->fichier->getDroits();
		}

		public function testVerifierExistence() {
			file_put_contents(vfsStream::url('testPath/page.html'), 'some content');

			$this->fichier->setFichierConfig('page.html', vfsStream::url('testPath'), false);
			$this->assertTrue($this->fichier->fichierExiste());
		}

		public function testVerifierExistenceFalse() {
			$this->fichier->setFichierConfig('monFichier.txt', '/chemin/daccess/');
			$this->assertFalse($this->fichier->fichierExiste());
		}

		public function testVerifierExistenceDossier() {
			mkdir(vfsStream::url('testPath/nouveauDossier'));
			$this->fichier->setCheminAcces('nouveauDossier/');
			$this->assertTrue($this->fichier->dossierExiste());
		}

		public function testVerifierExistenceDossierFalse() {
			$this->fichier->setCheminAcces('path/fake/');
			$this->assertFalse($this->fichier->dossierExiste());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10103
		 */
		public function testChargerFichierInexistant() {
			$this->fichier->setFichierConfig('monFichier.txt', '/chemin/daccess/');

			$this->fichier->chargerFichier();
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10104
		 */
		public function testChargerExtensionInconnu() {
			file_put_contents(vfsStream::url('testPath/fichier.fake'), 'some content');

			$this->fichier->setFichierConfig('fichier.fake', '');

			$this->fichier->chargerFichier();
		}

		public function testCharger() {
			$abstractChargeur = $this->createMock('AbstractChargeurFichier',
				array('chargerFichier', vfsStream::url('testPath/fichier.php'),  array('paris' => 'yeah'))
			);

			/** @var $fichier Fichier */
			$fichier = $this->createMock('Fichier',
				array('fichierExiste', '', true),
				array('getChargeurClass', '', $abstractChargeur)
			);

			$fichier->setFichierConfig('fichier.php', vfsStream::url('testPath'), false);

			$this->assertEquals(array('paris' => 'yeah'), $fichier->chargerFichier());
		}

		public function testCreerFichier() {
			$this->fichier->setFichierConfig('party.png', '');

			$this->assertFalse($this->fichier->fichierExiste());

			$this->fichier->creerFichier();

			$this->assertTrue($this->fichier->fichierExiste());
		}

		public function testCreerFichierDroitsDefaut0777() {
			$this->fichier->setFichierConfig('party.png', '');

			$this->fichier->creerFichier();

			$this->assertEquals('0777', $this->fichier->getDroits());
		}

		public function testCreerFichierAvecDroits() {
			$this->fichier->setFichierConfig('party.png', '');

			$this->fichier->creerFichier('0421');

			$this->assertEquals('0421', $this->fichier->getDroits());
		}

		/**
		 * @expectedException     \Serveur\Exceptions\Exceptions\FichierException
		 * @expectedExceptionCode 10105
		 */
		public function testCreerDansDossierInexistant() {
			$this->fichier->setFichierConfig('party.png', '/path/');

			$this->fichier->creerFichier();
		}
	}