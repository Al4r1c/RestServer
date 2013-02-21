<?php
	namespace Serveur\Lib\FichierChargement;

	class Yaml extends AbstractChargeurFichier {
		public function chargerFichier($locationFichier) {
			return \Spyc::YAMLLoad($locationFichier);
		}
	}