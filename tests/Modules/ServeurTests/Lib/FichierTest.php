<?php
    namespace Modules\ServeurTests\Lib;

    use Modules\TestCase;
    use Modules\MockArg;
    use Serveur\Lib\Fichier;

    class FichierTest extends TestCase
    {
        /** @var Fichier */
        private $fichier;

        public function setUp()
        {
            $this->fichier = \Serveur\Utils\FileManager::getFichier();
        }

        public function testFileSystem()
        {
            $fileSystem = $this->createMock('FileSystem');

            $this->fichier->setFileSystem($fileSystem);

            $this->assertEquals($fileSystem, $this->fichier->getFileSystem());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetNotFileSystem()
        {
            $this->fichier->setFileSystem('should be object');
        }

        public function testNomFichier()
        {
            $this->fichier->setNomFichier('monFichier.txt');

            $this->assertEquals('monFichier.txt', $this->fichier->getNomFichier());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testNomFichierNotString()
        {
            $this->fichier->setNomFichier(5);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10200
         */
        public function testNomFichierNonNull()
        {
            $this->fichier->setNomFichier(' ');
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10201
         */
        public function testNomFichierInvalid()
        {
            $this->fichier->setNomFichier('monFichierSansExtension');
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testCheminDaccesNonString()
        {
            $this->fichier->setRepertoireFichier(23.3);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10202
         */
        public function testCheminDaccesNull()
        {
            $this->fichier->setRepertoireFichier(' ');
        }

        public function testCheminDacces()
        {
            $this->fichier->setRepertoireFichier('/path\\\\to///fichier////');

            $this->assertEquals(
                BASE_PATH . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR .
                'fichier' . DIRECTORY_SEPARATOR,
                $this->fichier->getRepertoireFichier()
            );
        }

        public function testCheminDaccesAbsolue()
        {
            $this->fichier->setRepertoireFichier(BASE_PATH . '/path/to/fichier/');

            $this->assertEquals(
                BASE_PATH . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR .
                'fichier' . DIRECTORY_SEPARATOR,
                $this->fichier->getRepertoireFichier()
            );
        }

        public function testGetCheminComplet()
        {
            $this->fichier->setRepertoireFichier('/path/');
            $this->fichier->setNomFichier('comeatme.log');

            $this->assertEquals(
                BASE_PATH . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'comeatme.log',
                $this->fichier->getCheminCompletFichier()
            );
        }

        public function testSetFichierConfig()
        {
            $this->fichier->setFichierParametres('monFichier.txt', '/chemin\\dacces/');

            $this->assertEquals(
                BASE_PATH . DIRECTORY_SEPARATOR . 'chemin' . DIRECTORY_SEPARATOR . 'dacces' . DIRECTORY_SEPARATOR .
                'monFichier.txt',
                $this->fichier->getCheminCompletFichier()
            );
        }

        public function testVerifierExistence()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true, array('c:\\wwww\\')),
                new MockArg('fichierExiste', true, array('c:\\wwww\\chemin\\monFichier.png'))
            );

            $fileSystem->initialiser('Windows', 'c:\\wwww\\');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('monFichier.png', '/chemin/');

            $this->assertTrue($this->fichier->fichierExiste());
        }

        public function testVerifierExistenceFalse()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true, array('c:\\wwww\\')),
                new MockArg('fichierExiste', false, array('c:\\wwww\\chemin\\monFichier.png'))
            );

            $fileSystem->initialiser('Windows', 'c:\\wwww\\');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('monFichier.png', '/chemin/');

            $this->assertFalse($this->fichier->fichierExiste());
        }

        public function testChargerFichier()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true, array('/data/www/')),
                new MockArg('fichierExiste', true, array('/data/www/chemin/variables.php')),
                new MockArg('chargerFichier', array('VAR1' => 'PARAM1'), array('/data/www/chemin/variables.php'))
            );

            $fileSystem->initialiser('Linux', '/data/www/');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('variables.php', '/chemin/');

            $this->assertEquals(array('VAR1' => 'PARAM1'), $this->fichier->chargerFichier());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10203
         */
        public function testChargerFichierInexistant()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true, array('/data/www/')),
                new MockArg('fichierExiste', false, array('/data/www/chemin/monFichierFAke.php'))
            );

            $fileSystem->initialiser('Linux', '/data/www/');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('monFichierFAke.php', '/chemin/');

            $this->fichier->chargerFichier();
        }

        public function testCreerFichier()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true),
                new MockArg('creerFichier', true, array('/data/www/chemin/party.png'))
            );

            $fileSystem->initialiser('Linux', '/data/www/');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('party.png', 'chemin/');

            $this->assertTrue($this->fichier->creerFichier());
        }

        public function testNeRecreerPasFichierExisteDeja()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true),
                new MockArg('fichierExiste', true, array('/data/www/chemin/party.png'))
            );

            $fileSystem->initialiser('Linux', '/data/www/');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('party.png', 'chemin/');

            $this->assertTrue($this->fichier->creerFichier());
        }


        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10204
         */
        public function testCreerDansDossierInexistant()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true, array('c:\\www\\')),
                new MockArg('dossierExiste', false, array('c:\\www\\path\\'))
            );

            $fileSystem->initialiser('Windows', 'c:\\www\\');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('party.png', '/path/');

            $this->fichier->creerFichier();
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10205
         */
        public function testCreerBug()
        {
            /** @var $fileSystem \Serveur\Lib\FileSystem */
            $fileSystem = $this->createMock(
                'FileSystem',
                new MockArg('dossierExiste', true),
                new MockArg('creerFichier', null, array('c:\\www\\path\\heya.log'))
            );

            $fileSystem->initialiser('Windows', 'c:\\www\\');

            $this->fichier->setFileSystem($fileSystem);

            $this->fichier->setFichierParametres('heya.log', '/path/');

            $this->fichier->creerFichier();
        }

        public function testEcrire()
        {
            \org\bovigo\vfs\vfsStreamWrapper::register();
            \org\bovigo\vfs\vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('testPath'));
            $fileSystem = new \Serveur\Lib\FileSystem();
            $fileSystem->setBasePath(\org\bovigo\vfs\vfsStream::url('testPath'));

            $this->fichier->setFileSystem($fileSystem);
            $this->fichier->setNomFichier('coucou.txt');
            $this->fichier->setRepertoireFichier('newDossier');

            mkdir(\org\bovigo\vfs\vfsStream::url('testPath/newDossier/'));

            $this->fichier->creerFichier();
            $this->fichier->ecrireDansFichier("Nouvelle ligne\n");
            $this->fichier->ecrireDansFichier("Heya");

            $this->assertEquals("Nouvelle ligne\nHeya", file_get_contents($this->fichier->getCheminCompletFichier()));
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 10203
         */
        public function testEcrireFichierInexistant()
        {
            $this->fichier->ecrireDansFichier("Nouvelle ligne\n");
        }
    }