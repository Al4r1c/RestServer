<?php
	namespace Serveur\I18n;

	use DOMXPath;

	class TradManager {
		private $fichierTraductionDefaut;

		public function setFichierTraduction(\DOMDocument $fichierTradDef) {
			$this->fichierTraductionDefaut = $fichierTradDef;
		}

		private function getTraduction($section, $identifier) {
			$xpath = new DomXpath($this->fichierTraductionDefaut);

			foreach($xpath->query('//' . $section . '/message[@code="' . $identifier . '"]') as $unMessage) {
				$message = $unMessage->nodeValue;
			}

			if(isset($message)) {
				return $message;
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