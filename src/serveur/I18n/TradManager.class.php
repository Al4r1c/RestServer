<?php
	namespace Serveur\I18n;

	use Serveur\Lib\XMLParser\XMLParser;

	class TradManager {
		/** @var XMLParser */
		private $fichierTraductionDefaut;

		public function setFichierTraduction(XMLParser $fichierTradDef) {
			$this->fichierTraductionDefaut = $fichierTradDef;
		}

		private function getTraduction($section, $identifier) {
			$xmlElementsCorrespondants = $this->fichierTraductionDefaut->getConfigValeur($section.'.message[code='.$identifier.']');

			if(isset($xmlElementsCorrespondants)) {
				return $xmlElementsCorrespondants[0]->getValeur();
			} else {
				trigger_notice_apps(40006, $section, $identifier);

				return $section.'.'.$identifier;
			}
		}

		public function recupererChaine($contenu) {
			if(preg_match_all('/{.*?}/', $contenu, $stringTrouve)) {
				foreach(array_unique($stringTrouve[0]) as $valeur) {
					$contenu = str_replace($valeur, $this->getTraduction(substr($valeur, 1, strpos($valeur, '.') - 1), substr($valeur, strpos($valeur, '.') + 1, -1)), $contenu);
				}
			}

			return $contenu;
		}
	}