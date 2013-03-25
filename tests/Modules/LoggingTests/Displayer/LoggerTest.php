<?php
namespace Tests\LoggingTests\Displayer;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use Logging\Displayer\Logger;
use Serveur\GestionErreurs\Types\Error;
use Serveur\GestionErreurs\Types\Notice;
use Tests\MockArg;
use Tests\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class LoggerTest extends TestCase
{
    /** @var Logger */
    private $_logger;

    public function setUp()
    {
        $this->_logger = new Logger();
    }

    /**
     * @param $chemin
     * @return File
     */
    public function getFile($chemin)
    {
        $alaroxFileManager = new AlaroxFile();

        return $alaroxFileManager->getFile($chemin);
    }

    public function getFakeTradManager()
    {
        $functionCallback = function ($object) {
            return $object;
        };

        $tradManager = $this->createMock('TradManager', new MockArg('recupererChaineTraduite', $functionCallback));

        return $tradManager;
    }

    public function activeFakeFileSystem()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));
    }

    public function testSetFichierLogAcces()
    {
        /** @var $fichierAcces File */
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
            'RequeteManager', new MockArg('getDateRequete', $dateRequete), new MockArg('getIp', '127.0.0.1'),
            new MockArg('getMethode', 'GET'), new MockArg('getUriVariables', array('edit')),
            new MockArg('getParametres', array('param1' => 'var1'))
        );

        $this->activeFakeFileSystem();
        $fichierAcces = $this->getFile(vfsStream::url('root') . '/acces.log');
        $fichierAcces->createFile();
        $this->_logger->setFichierLogAcces($fichierAcces);
        $this->_logger->setTradManager($this->getFakeTradManager());

        $this->_logger->ecrireLogRequete($restRequete);

        $contenu = file_get_contents($fichierAcces->getPathToFile());

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
            'ObjetReponse', new MockArg('getStatusHttp', 200), new MockArg('getFormat', 'json')
        );

        $this->activeFakeFileSystem();
        $fichierAcces = $this->getFile(vfsStream::url('root') . '/acces.log');
        $fichierAcces->createFile();
        $this->_logger->setFichierLogAcces($fichierAcces);
        $this->_logger->setTradManager($this->getFakeTradManager());

        $this->_logger->ecrireLogReponse($restReponse);

        $contenu = file_get_contents($fichierAcces->getPathToFile());

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
        $this->_logger->ecrireLogReponse($this->getMockObjetReponse());
    }

    public function testEcrireErreur()
    {
        $uneErreur = new Error(20000);
        $uneErreur->setMessage("Mon message erreur");

        $this->activeFakeFileSystem();
        $fichierErreurs = $this->getFile(vfsStream::url('root') . '/erreur.log');
        $fichierErreurs->createFile();
        $this->_logger->setFichierLogErreur($fichierErreurs);
        $this->_logger->setTradManager($this->getFakeTradManager());

        $this->_logger->ecrireErreurLog($uneErreur);

        $contenu = file_get_contents($fichierErreurs->getPathToFile());

        $this->assertContains('{trad.fatalerror}', $contenu);
        $this->assertContains('{errorType.200}', $contenu);
        $this->assertContains($uneErreur->getDate()->format('d-m-Y H:i:s'), $contenu);
        $this->assertContains('Mon message erreur', $contenu);
        $this->assertContains('20000', $contenu);
    }

    public function testEcrireNotice()
    {
        $uneErreur = new Notice(E_USER_NOTICE);
        $uneErreur->setMessage("Ma notice");

        $this->activeFakeFileSystem();
        $fichierErreurs = $this->getFile(vfsStream::url('root') . '/erreur.log');
        $fichierErreurs->createFile();
        $this->_logger->setFichierLogErreur($fichierErreurs);
        $this->_logger->setTradManager($this->getFakeTradManager());

        $this->_logger->ecrireErreurLog($uneErreur);

        $contenu = file_get_contents($fichierErreurs->getPathToFile());

        $this->assertContains('{trad.notice}', $contenu);
        $this->assertContains('{errorType.1}', $contenu);
        $this->assertContains($uneErreur->getDate()->format('d-m-Y H:i:s'), $contenu);
        $this->assertContains('Ma notice', $contenu);
        $this->assertContains('1024', $contenu);
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