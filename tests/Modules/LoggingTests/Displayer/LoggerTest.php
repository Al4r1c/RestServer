<?php
    namespace Modules\LoggingTests\Displayer;

    use Modules\TestCase;
    use Modules\MockArg;
    use org\bovigo\vfs\vfsStreamWrapper;
    use org\bovigo\vfs\vfsStream;
    use Serveur\Lib\Fichier;
    use Serveur\GestionErreurs\Types\Error;
    use Serveur\GestionErreurs\Types\Notice;

    class LoggerTest extends TestCase
    {
        /** @var \Logging\Displayer\Logger */
        private $_logger;

        public function setUp()
        {
            $this->_logger = new \Logging\Displayer\Logger();
        }

        public function getFakeTradManager()
        {
            $functionCallback = function ($object) {
                return $object;
            };

            $tradManager = $this->createMock('TradManager', new MockArg('recupererChaineTraduite', $functionCallback));

            return $tradManager;
        }

        public function getFakeFileSystem()
        {
            vfsStreamWrapper::register();
            vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('root'));

            $fileSystem = new \Serveur\Lib\FileSystem();
            $fileSystem->setBasePath(vfsStream::url('root'));

            return $fileSystem;
        }

        public function testSetFichierLogAcces()
        {
            $fichierAcces = $this->createMock('Fichier');

            $this->_logger->setFichierLogAcces($fichierAcces);

            $this->assertAttributeEquals($fichierAcces, '_fichierLogAcces', $this->_logger);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testSetFichierLogAccesErrone()
        {
            $this->_logger->setFichierLogAcces(null);
        }

        public function testSetFichierLogErreurs()
        {
            $fichierErreurs = $this->createMock('Fichier');

            $this->_logger->setFichierLogErreur($fichierErreurs);

            $this->assertAttributeEquals($fichierErreurs, '_fichierLogErreur', $this->_logger);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testSetFichierLogErreurErrone()
        {
            $this->_logger->setFichierLogErreur(5);
        }

        public function testSetTradManager()
        {
            $tradManager = $this->createMock('TradManager');

            $this->_logger->setTradManager($tradManager);

            $this->assertAttributeEquals($tradManager, '_tradManager', $this->_logger);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testSetTradManagerErrone()
        {
            $this->_logger->setTradManager('something');
        }

        public function testEcrireRequete()
        {
            $dateRequete = new \DateTime();

            $restRequete = $this->createMock(
                'RequeteManager',
                new MockArg('getDateRequete', $dateRequete),
                new MockArg('getIp', '127.0.0.1'),
                new MockArg('getMethode', 'GET'),
                new MockArg('getUriVariables', array('edit')),
                new MockArg('getParametres', array('param1' => 'var1'))
            );

            $fichierAcces = new Fichier();
            $fichierAcces->setFileSystem($this->getFakeFileSystem());
            $fichierAcces->setFichierParametres('acces.log', vfsStream::url('root'));
            $fichierAcces->creerFichier();
            $this->_logger->setFichierLogAcces($fichierAcces);
            $this->_logger->setTradManager($this->getFakeTradManager());

            $this->_logger->ecrireLogRequete($restRequete);

            $contenu = file_get_contents($fichierAcces->getCheminCompletFichier());

            $this->assertContains($dateRequete->format('d-m-Y H:i:s'), $contenu);
            $this->assertContains('127.0.0.1', $contenu);
            $this->assertContains('GET', $contenu);
            $this->assertContains('/edit', $contenu);
            $this->assertContains('param1 => var1', $contenu);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testEcrireAccesWrongRequete()
        {
            $this->_logger->ecrireLogRequete(null);
        }

        /**
         * @expectedException \Exception
         */
        public function testEcrireRequeteFileError()
        {
            $this->_logger->ecrireLogRequete($this->createMock('RequeteManager'));
        }

        public function testEcrireReponse()
        {
            $restReponse = $this->createMock(
                'ReponseManager',
                new MockArg('getStatus', 200),
                new MockArg('getFormatRetour', 'json')
            );

            $fichierAcces = new Fichier();
            $fichierAcces->setFileSystem($this->getFakeFileSystem());
            $fichierAcces->setFichierParametres('acces.log', vfsStream::url('root'));
            $fichierAcces->creerFichier();
            $this->_logger->setFichierLogAcces($fichierAcces);
            $this->_logger->setTradManager($this->getFakeTradManager());

            $this->_logger->ecrireLogReponse($restReponse);

            $contenu = file_get_contents($fichierAcces->getCheminCompletFichier());

            $this->assertContains('200', $contenu);
            $this->assertContains('json', $contenu);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testEcrireAccesWrongReponse()
        {
            $this->_logger->ecrireLogReponse(null);
        }

        /**
         * @expectedException \Exception
         */
        public function testEcrireReponseFileError()
        {
            $this->_logger->ecrireLogReponse($this->createMock('ReponseManager'));
        }

        public function testEcrireErreur()
        {
            $uneErreur = new Error(10000);
            $uneErreur->setMessage("Mon message erreur");

            $fichierErreurs = new Fichier();
            $fichierErreurs->setFileSystem($this->getFakeFileSystem());
            $fichierErreurs->setFichierParametres('erreur.log', vfsStream::url('root'));
            $fichierErreurs->creerFichier();
            $this->_logger->setFichierLogErreur($fichierErreurs);
            $this->_logger->setTradManager($this->getFakeTradManager());

            $this->_logger->ecrireErreurLog($uneErreur);

            $contenu = file_get_contents($fichierErreurs->getCheminCompletFichier());

            $this->assertContains('{trad.fatalerror}', $contenu);
            $this->assertContains($uneErreur->getDate()->format('d-m-Y H:i:s'), $contenu);
            $this->assertContains('Mon message erreur', $contenu);
            $this->assertContains('10000', $contenu);
        }

        public function testEcrireNotice()
        {
            $uneErreur = new Notice(10000);
            $uneErreur->setMessage("Ma notice");

            $fichierErreurs = new Fichier();
            $fichierErreurs->setFileSystem($this->getFakeFileSystem());
            $fichierErreurs->setFichierParametres('erreur.log', vfsStream::url('root'));
            $fichierErreurs->creerFichier();
            $this->_logger->setFichierLogErreur($fichierErreurs);
            $this->_logger->setTradManager($this->getFakeTradManager());

            $this->_logger->ecrireErreurLog($uneErreur);

            $contenu = file_get_contents($fichierErreurs->getCheminCompletFichier());

            $this->assertContains('{trad.notice}', $contenu);
            $this->assertContains($uneErreur->getDate()->format('d-m-Y H:i:s'), $contenu);
            $this->assertContains('Ma notice', $contenu);
            $this->assertContains('10000', $contenu);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testEcrireErrone()
        {
            $this->_logger->ecrireErreurLog(new \StdClass());
        }

        /**
         * @expectedException \Exception
         */
        public function testEcrireErreurFileError()
        {
            $this->_logger->ecrireErreurLog(new Error(555, 'mess'));
        }
    }