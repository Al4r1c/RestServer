<?php
	namespace Serveur\I18n;

	use Serveur\Exceptions\Exceptions\I18nManagerException;

	class I18nManager {

		private $langueDefaut;
		private $languesDisponibles;
		private static $defaultXml = '<?xml version="1.0" encoding="UTF-8"?><language lang="en"><trad><message code="notice">Notice</message><message code="warning">Warning</message><message code="error">Error</message><message code="i18nManager">Internationalization Manager</message><message code="tradManager">Gestionnaire de traduction</message></trad><errorMessage><message code="40007">No valid translation file set or found.</message><message code="40101">Translation for the entity "%s.%s" not found.</message></errorMessage></language>';

		public function setConfig(\Serveur\Config\Config $configuration) {
			$this->setLangueDefaut($configuration->getConfigValeur('config.default_lang'));
			$this->setLangueDispo($configuration->getConfigValeur('languages'));
		}

		public function setLangueDefaut($langueDefaut) {
			if(isNull($langueDefaut)) {
				throw new I18nManagerException(40000, 500);
			}

			$this->langueDefaut = $langueDefaut;
		}

		public function setLangueDispo(array $languesDispo) {
			if(isNull($languesDispo)) {
				throw new I18nManagerException(40001, 500);
			}

			$this->languesDisponibles = $languesDispo;
		}

		/** @return \Serveur\Lib\XMLParser\XMLParser */
		public function getFichierTraduction() {
			if(array_key_exists(strtoupper($this->langueDefaut), $this->languesDisponibles)) {
				$nomFichierLangueDefaut = $this->languesDisponibles[strtoupper($langueDefautUtilisee = $this->langueDefaut)];
			} else {
				$nomFichierLangueDefaut = reset($this->languesDisponibles);
				trigger_notice_apps(40002, $this->langueDefaut, $langueDefautUtilisee = key($this->languesDisponibles));
			}

			$fichierTraductionParDefaut = $this->getFichier($nomFichierLangueDefaut);

			if($fichierTraductionParDefaut->fichierExiste() && $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut)->isValide()) {
				return $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut);
			} else {
				if(($langueChoisiAleatoirement = $this->getUneTraductionAleatoire()) !== false) {
					if(!$fichierTraductionParDefaut->fichierExiste()) {
						trigger_notice_apps(40003, $langueDefautUtilisee, $fichierTraductionParDefaut->getCheminCompletFichier(), key($langueChoisiAleatoirement));
					} else {
						trigger_notice_apps(40004, $langueDefautUtilisee, $fichierTraductionParDefaut->getCheminCompletFichier(), $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut)->getErreurMessage(), key($langueChoisiAleatoirement));
					}

					return $this->recupererXmlParserDepuisFichier(reset($langueChoisiAleatoirement));
				} else {
					trigger_notice_apps(40007);

					$newXmlParser =  new \Serveur\Lib\XMLParser\XMLParser();
					$newXmlParser->setContenu(self::$defaultXml);

					return $newXmlParser;
				}
			}
		}

		/** @return \Serveur\Lib\Fichier */
		protected function getFichier($nomFichier) {
			$fichier = \Serveur\Utils\FileManager::getFichier();
			$fichier->setFichierParametres($nomFichier . '.xml', '/public/i18n');

			return $fichier;
		}

		private function getUneTraductionAleatoire() {
			foreach($this->languesDisponibles as $uneLangueDispo => $classeLangue) {
				$traductionDisponible = $this->getFichier($classeLangue);

				if(!$traductionDisponible->fichierExiste()) {
					trigger_notice_apps(40005, $uneLangueDispo, $traductionDisponible->getCheminCompletFichier());
				} elseif(!$this->recupererXmlParserDepuisFichier($traductionDisponible)->isValide()) {
					trigger_notice_apps(40006, $uneLangueDispo, $this->recupererXmlParserDepuisFichier($traductionDisponible)->getErreurMessage(), $traductionDisponible->getCheminCompletFichier());
				} else {
					return array($uneLangueDispo => $traductionDisponible);
				}
			}

			return false;
		}

		/** @return \Serveur\Lib\XMLParser\XMLParser */
		private function recupererXmlParserDepuisFichier(\Serveur\Lib\Fichier $fichier) {
			return $fichier->chargerFichier();
		}
	}