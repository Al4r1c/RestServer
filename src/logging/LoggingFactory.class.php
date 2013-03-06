<?php
    namespace Logging;

    class LoggingFactory {

        private static $_langueDefaut = 'French';
        private static $_langueDispo = array('French' => 'fr', 'English' => 'en');

        /**
         * @param string $loggingMethode
         * @return Displayer\AbstractDisplayer
         * @throws \Exception
         */
        public static function getLogger($loggingMethode) {
            switch ($loggingMethode) {
                case 'logger':
                    $logger = new \Logging\Displayer\Logger();
                    $logger->setTradManager(self::getI18n());
                    $logger->setFichierLogErreur(self::creerFichierSiNexistePas('errors.log'));
                    $logger->setFichierLogAcces(self::creerFichierSiNexistePas('access.log'));
                    break;
                default:
                    throw new \Exception();
                    break;
            }

            return $logger;
        }

        /**
         * @return I18n\TradManager
         */
        private static function getI18n() {
            $i18nManager = new \Logging\I18n\I18nManager();
            $i18nManager->setConfig(self::$_langueDefaut, self::$_langueDispo);

            $tradManager = new \Logging\I18n\TradManager();
            $tradManager->setFichierTraduction($i18nManager->getFichierTraduction());

            return $tradManager;
        }

        /**
         * @param string $nomFichier
         * @return \Serveur\Lib\Fichier
         */
        private static function creerFichierSiNexistePas($nomFichier) {
            $fichier = \Serveur\Utils\FileManager::getFichier();
            $fichier->setFichierParametres($nomFichier, BASE_PATH . '/log');
            $fichier->creerFichier('0700');

            return $fichier;
        }
    }