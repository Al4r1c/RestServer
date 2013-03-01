<?php
	namespace Logging;

	class LoggingFactory {

		private static $langueDefaut = 'French';
		private static $langueDispo = array(
			'French' => 'fr',
			'English' => 'en'
		);

		public static function getLogger($loggingMethode) {
			if(class_exists($displayerName = '\\' . __NAMESPACE__ . '\Displayer\\' . ucfirst(strtolower($loggingMethode)))) {
				/** @var $logger \Logging\Displayer\AbstractDisplayer */
				$logger = new $displayerName();
				$logger->setTradManager(self::getI18n());

				return $logger;
			} else {
				throw new \Exception();
			}
		}

		private static function getI18n() {
			$I18nManager = new \Logging\I18n\I18nManager();
			$I18nManager->setConfig(self::$langueDefaut, self::$langueDispo);

			$tradManager = new \Logging\I18n\TradManager();
			$tradManager->setFichierTraduction($I18nManager->getFichierTraduction());

			return $tradManager;
		}
	}