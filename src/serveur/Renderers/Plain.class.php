<?php
	namespace Serveur\Renderers;

	class Plain extends \Serveur\Renderers\AbstractRenderer {
		public function render(array $donnees) {
			$valeurs = '';
			foreach($donnees as $clef => $valeur) {
				$valeurs .= $clef .' => '. $valeur."\n";
			}

			return $valeurs;
		}
	}