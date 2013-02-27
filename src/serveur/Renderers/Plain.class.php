<?php
	namespace Serveur\Renderers;

	class Plain extends \Serveur\Renderers\AbstractRenderer {
		public function render(array $donnees) {
			return $this->arrayToString($donnees);
		}

		private function arrayToString(array $donnees, $level = 0) {
			$valeurs = '';

			foreach($donnees as $clef => $valeur) {
				for($i = 0; $i < $level; $i++) {
					$valeurs .= "\t";
				}

				if(is_array($valeur)) {
					$valeurs .= $clef . " => \n" . $this->arrayToString($valeur, ($level + 1));
				} else {
					$valeurs .= $clef . " => " . $valeur . "\n";
				}
			}

			return $valeurs;
		}
	}