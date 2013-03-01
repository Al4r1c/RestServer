<?php
	namespace Serveur\Rest;

	use Serveur\Utils\Tools;

	class HeaderManager {
		private $headers = array();

		public function ajouterHeader($champ, $valeur) {
			if(!Tools::isValideHeader($champ)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20400, 500);
			}

			$this->headers[$champ] = $valeur;
		}

		public function envoyerHeaders() {
			foreach($this->headers as $champHeader => $valeurHeader) {
				header($champHeader . ': ' . $valeurHeader, true);
			}
		}
	}