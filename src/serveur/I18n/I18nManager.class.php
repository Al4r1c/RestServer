<?php
	namespace Serveur\I18n;

	use Serveur\Lib\Fichier;

	class I18nManager {

		private $langueDefaut;
		private $languesDisponibles;
		private static $defaultXml = '<?xml version="1.0" encoding="UTF-8"?><language lang="en"><trad><message code="notice">Notice</message><message code="warning">Warning</message><message code="error">Error</message></trad><errorMessage><message code="40005">No valid translation file set/found.</message><message code="40006">Translation for the entity "%s.%s" not found.</message></errorMessage></language>';

		public function setConfig(\Serveur\Config\Config $configuration) {
			$this->langueDefaut = $configuration->getConfigValeur('config.default_lang');
			$this->languesDisponibles = $configuration->getConfigValeur('languages');
		}

		public function getTradFileDefaut() {
			if (array_key_exists(strtoupper($this->langueDefaut), $this->languesDisponibles) !== false) {
				$langueDefautClasse = $this->languesDisponibles[strtoupper($langueDefautUtilisee = $this->langueDefaut)];
			} else {
				$langueDefautClasse = reset($this->languesDisponibles);
				trigger_notice_apps(40000, $this->langueDefaut, $langueDefautUtilisee = key($this->languesDisponibles));
			}

			$traductionObjetDefaut = $this->chargerFichier($langueDefautClasse);

			if(is_null($traductionObjetDefaut) || $traductionObjetDefaut === false) {
				foreach($this->languesDisponibles as $uneLangueDispo => $classeLangue) {
					$traductionDisponible = $this->chargerFichier($classeLangue);

					if(is_null($traductionDisponible)) {
						trigger_notice_apps(40003, $uneLangueDispo, BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $classeLangue . '.xml');
					} elseif($traductionDisponible === false) {
						trigger_notice_apps(40004, $uneLangueDispo, BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $classeLangue . '.xml');
					} else {
						$defaultTraductionObject = $traductionDisponible;
						break;
					}
				}

				if(isset($defaultTraductionObject)) {
					if(is_null($traductionObjetDefaut)) {
						trigger_notice_apps(40001, $langueDefautUtilisee, BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $langueDefautClasse . '.xml', $uneLangueDispo);
					} else {
						trigger_notice_apps(40002, $langueDefautUtilisee, BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $langueDefautClasse . '.xml', $uneLangueDispo);
					}
				} else {
					trigger_notice_apps(40005, $this->langueDefaut, $langueDefautUtilisee = key($this->languesDisponibles));
					$defaultTraductionObject = new \DOMDocument();
					$defaultTraductionObject->loadXML(self::$defaultXml);
				}
			} else {
				$defaultTraductionObject = $traductionObjetDefaut;
			}

			return $defaultTraductionObject;
		}

		private function chargerFichier($nomFichier) {
			$fichier = new Fichier();
			$fichier->setBasePath(BASE_PATH);
			$fichier->setFichierConfig($nomFichier.'.xml', '/public/i18n');
			if(!$fichier->fichierExiste()) {
				return null;
			} elseif(($defaultTraductionObject = $fichier->chargerFichier()) === false) {
				return false;
			} else {
				return $defaultTraductionObject;
			}
		}
	}