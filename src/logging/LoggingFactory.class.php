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
            if (class_exists($displayerName = '\\' . __NAMESPACE__ . '\Displayer\\' . ucfirst(strtolower($loggingMethode)))) {
                /** @var $logger \Logging\Displayer\AbstractDisplayer */
                $logger = new $displayerName();
                $logger->setTradManager(self::getI18n());

                return $logger;
            } else {
                throw new \Exception();
            }
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
    }