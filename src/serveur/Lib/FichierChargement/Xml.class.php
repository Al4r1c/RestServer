<?php
	namespace Serveur\Lib\FichierChargement;

	use DOMDocument;

	class Xml extends AbstractChargeurFichier {
		public function chargerFichier($locationFichier) {
			$valeurBufferPrecedente = libxml_use_internal_errors(true);
			$domObjet = new DomDocument();

			$domObjet->load($locationFichier);
			if(!$domObjet->validate()) {
				$domObjet = false;
			}

			libxml_clear_errors();
			libxml_use_internal_errors($valeurBufferPrecedente);

			return $domObjet;
		}
	}