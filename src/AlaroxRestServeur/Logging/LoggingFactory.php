<?php
namespace AlaroxRestServeur\Logging;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxRestServeur\Logging\Displayer\AbstractDisplayer;
use AlaroxRestServeur\Logging\Displayer\Logger;
use AlaroxRestServeur\Logging\I18n\I18nManager;
use AlaroxRestServeur\Logging\I18n\TradManager;

class LoggingFactory
{
    private static $_langueDefaut = 'French';
    private static $_langueDispo = array('French' => 'fr', 'English' => 'en');

    /**
     * @param string $loggingMethode
     * @param string $logFolder
     * @throws \InvalidArgumentException
     * @return AbstractDisplayer
     */
    public static function getLogger($loggingMethode, $logFolder)
    {
        switch ($loggingMethode) {
            case 'logger':
                $logger = new Logger();
                $logger->setTradManager(self::getI18n());
                $logger->setFichierLogErreur(self::creerFichierSiNexistePas('errors.log', $logFolder));
                $logger->setFichierLogAcces(self::creerFichierSiNexistePas('access.log', $logFolder));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid displayer name %s.', $loggingMethode));
                break;
        }

        return $logger;
    }

    /**
     * @return TradManager
     */
    private static function getI18n()
    {
        $internationalizationManager = new I18nManager();
        $internationalizationManager->setConfig(self::$_langueDefaut, self::$_langueDispo);

        $tradManager = new TradManager();
        $tradManager->setFichierTraduction($internationalizationManager->getFichierTraduction());

        return $tradManager;
    }

    /**
     * @param string $nomFichier
     * @param string $dossierLog
     * @return File
     */
    private static function creerFichierSiNexistePas($nomFichier, $dossierLog)
    {
        $alaroxFileManager = new AlaroxFile();

        $file = $alaroxFileManager->getFile($dossierLog . DIRECTORY_SEPARATOR . $nomFichier);

        $file->createFile();

        return $file;
    }
}