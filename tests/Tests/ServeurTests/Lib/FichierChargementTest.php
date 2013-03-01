<?php
	namespace Tests\ServeurTests\Lib;

	include_once(__DIR__ . '/../../../TestEnv.php');

	use Tests\TestCase;
	use org\bovigo\vfs\vfsStreamWrapper;
	use org\bovigo\vfs\vfsStream;

	class FichierChargementTest extends TestCase {
		public function setUp() {
			vfsStreamWrapper::register();
			vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));
		}

		public function testChargerPhp() {
			file_put_contents(vfsStream::url('testPath/fichier.php'), "<?php return array('its1' => 'var1'); ?>");

			$chargeur = new \Serveur\Lib\FichierChargement\Php();
			$this->assertEquals(array('its1' => 'var1'), $chargeur->chargerFichier(vfsStream::url('testPath/fichier.php')));
		}

		public function testChargerXml() {
			file_put_contents(vfsStream::url('testPath/fichier.xml'), "<?xml version=\"1.0\" encoding=\"UTF-8\"?><root>ok</root>");

			$chargeur = new \Serveur\Lib\FichierChargement\Xml();
			$this->assertThat($chargeur->chargerFichier(vfsStream::url('testPath/fichier.xml')),
				$this->logicalAnd(
					$this->logicalNot($this->isNull()),
					$this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
				)
			);
		}

		public function testChargerYaml() {
			file_put_contents(vfsStream::url('testPath/fichier.yaml'), "Test:\n\t-t1\n\t-t2");

			$chargeur = new \Serveur\Lib\FichierChargement\Yaml();
			$this->assertEquals(array('Test' => array('t1', 't2')), $chargeur->chargerFichier(vfsStream::url('testPath/fichier.yaml')));
		}
	}