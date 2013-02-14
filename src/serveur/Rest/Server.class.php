<?php
	namespace Serveur\Rest;

	use Serveur\Exceptions\Exceptions\RestRequeteException;

	class Server {

		private $serveurVariable;
		private $serveurDonnees;

		public function __construct(array $varServeur) {
			$this->setServeurVariable($varServeur);
			$this->setServeurDonnees($varServeur['REQUEST_METHOD']);
		}

		public function getServeurVariable() {
			return $this->serveurVariable;
		}

		public function getServeurMethode() {
			return $this->serveurVariable['REQUEST_METHOD'];
		}

		public function getServeurUri() {
			return $this->serveurVariable['REQUEST_URI'];
		}

		public function getServeurHttpAccept() {
			return $this->serveurVariable['HTTP_ACCEPT'];
		}

		public function setServeurVariable($serverVar) {
			$this->serveurVariable = $serverVar;
		}

		public function getServeurDonnees() {
			return $this->serveurDonnees;
		}

		public function setServeurDonnees($methode) {
			switch(strtoupper($methode)) {
				case 'GET':
					parse_str($this->serveurVariable['QUERY_STRING'], $arguments);
					break;
				case 'POST':
				case 'PUT':
				case 'DELETE':
					parse_str(file_get_contents('php://input'), $arguments);
					break;
				default:
					throw new RestRequeteException(10000, 405, $methode);
			}

			$this->serveurDonnees = $arguments;
		}
	}