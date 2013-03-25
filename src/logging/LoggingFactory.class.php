<?php
namespace Logging;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use Logging\Displayer\Logger;
use Logging\I18n\I18nManager;
use Logging\I18n\TradManager;

class LoggingFactory
{
    private static $_langueDefaut = 'French';
    private static $_langueDispo = array('French' => 'fr', 'English' => 'en');

    /**
     * @param string $loggingMethode
     * @throws \InvalidArgumentException
     * @return Displayer\AbstractDisplayer
     */
    public static function getLogger($loggingMethode)
    {
        switch ($loggingMethode) {
            case 'logger':
                $logger = new Logger();
                $logger->setTradManager(self::getI18n());
                $logger->setFichierLogErreur(self::creerFichierSiNexistePas('errors.log'));
                $logger->setFichierLogAcces(self::creerFichierSiNexistePas('access.log'));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid displayer name %s.', $loggingMethode));
                break;
        }

        return $logger;
    }

    /**
     * @return I18n\TradManager
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
     * @return File
     */
    private static function creerFichierSiNexistePas($nomFichier)
    {
        $alaroxFileManager = new AlaroxFile();

        return $alaroxFileManager->getFile(BASE_PATH . '/log/' . $nomFichier);
    }
}