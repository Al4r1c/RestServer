<?php
    namespace Logging;

    use Logging\Displayer\Logger;
    use Logging\I18n\I18nManager;
    use Logging\I18n\TradManager;
    use Serveur\Utils\FileManager;

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
         * @return \Serveur\Lib\Fichier
         */
        private static function creerFichierSiNexistePas($nomFichier)
        {
            $fichier = FileManager::getFichier();
            $fichier->setFichierParametres($nomFichier, BASE_PATH . '/log');
            $fichier->creerFichier('0700');

            return $fichier;
        }
    }