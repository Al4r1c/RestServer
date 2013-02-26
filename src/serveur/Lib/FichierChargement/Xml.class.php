<?php
	namespace Serveur\Lib\FichierChargement;

	use Serveur\Lib\XMLParser\XMLParser;

	class Xml extends AbstractChargeurFichier {
		public function chargerFichier($locationFichier) {
			$donneesXml = file_get_contents($locationFichier);

			$xmlParsee = new XMLParser($donneesXml);

			return $xmlParsee;
		}
	}