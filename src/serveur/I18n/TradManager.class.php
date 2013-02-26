<?php
	namespace Serveur\I18n;

	use Serveur\Lib\XMLParser\XMLParser;
	use Serveur\Exceptions\Exceptions\TradManagerException;

	class TradManager {
		/** @var XMLParser */
		private $fichierTraductionDefaut;

		public function setFichierTraduction(XMLParser $fichierTradDef) {
			if(!$fichierTradDef->isValide()) {
				throw new TradManagerException(40100, 500);
			}

			$this->fichierTraductionDefaut = $fichierTradDef;
		}

		private function getTraduction($section, $identifier) {
			$xmlElementsCorrespondants = $this->fichierTraductionDefaut->getConfigValeur($section.'.message[code='.$identifier.']');

			if(isset($xmlElementsCorrespondants)) {
				return $xmlElementsCorrespondants[0]->getValeur();
			} else {
				trigger_notice_apps(40101, $section, $identifier);

				return '{'.$section.'.'.$identifier.'}';
			}
		}

		public function recupererChaineTraduite($contenu) {
			if(isNull($this->fichierTraductionDefaut)) {
				throw new TradManagerException(40102, 500);
			}

			if(preg_match_all('/{.*?}/', $contenu, $stringTrouve)) {
				foreach(array_unique($stringTrouve[0]) as $valeur) {
					$contenu = str_replace($valeur, $this->getTraduction(substr($valeur, 1, strpos($valeur, '.') - 1), substr($valeur, strpos($valeur, '.') + 1, -1)), $contenu);
				}
			}

			return $contenu;
		}
	}