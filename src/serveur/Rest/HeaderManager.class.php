<?php
	namespace Serveur\Rest;

	use Serveur\Utils\Tools;

	class HeaderManager {
		/**
		 * @var array
		 */
		private $headers = array();

		/**
		 * @param string $champ
		 * @param string $valeur
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function ajouterHeader($champ, $valeur) {
			if(!is_string($champ)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $champ);
			}

			if(!is_string($valeur)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $valeur);
			}

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