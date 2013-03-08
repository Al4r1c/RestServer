<?php
    namespace Logging;

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
                    $logger = new \Logging\Displayer\Logger();
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
            $internationalizationManager = new \Logging\I18n\I18nManager();
            $internationalizationManager->setConfig(self::$_langueDefaut, self::$_langueDispo);

            $tradManager = new \Logging\I18n\TradManager();
            $tradManager->setFichierTraduction($internationalizationManager->getFichierTraduction());

            return $tradManager;
        }

        /**
         * @param string $nomFichier
         * @return \Serveur\Lib\Fichier
         */
        private static function creerFichierSiNexistePas($nomFichier)
        {
            $fichier = \Serveur\Utils\FileManager::getFichier();
            $fichier->setFichierParametres($nomFichier, BASE_PATH . '/log');
            $fichier->creerFichier('0700');

            return $fichier;
        }
    }