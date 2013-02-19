<?php
	namespace Serveur\Rest;

	use Serveur\Utils\Tools;
	use Serveur\Exceptions\Exceptions\HeaderManagerException;

	class HeaderManager {
		private $headers = array();

		public function ajouterHeader($champ, $valeur) {
			if(!Tools::isValideHeader($champ)) {
				throw new HeaderManagerException(20300, 500);
			}

			$this->headers[$champ] = $valeur;
		}

		public function envoyerHeaders() {
			foreach($this->headers as $champHeader => $valeurHeader) {
				header($champHeader.': ' . $valeurHeader, true);
			}
		}
	}