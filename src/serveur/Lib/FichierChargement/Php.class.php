<?php
	namespace Serveur\Lib\FichierChargement;

	class Php extends AbstractChargeurFichier {
		public function chargerFichier($locationFichier) {
			return include $locationFichier;
		}
	}