<?php
	namespace Serveur\Rest;

	class Server {
		/**
		 * @var array
		 */
		private $serveurVariable;

		/**
		 * @var array
		 */
		private $serveurDonnees;

		/**
		 * @param array $varServeur
		 */
		public function setVarServeur(array $varServeur) {
			$this->setServeurVariable($varServeur);
			$this->setServeurDonnees($varServeur['REQUEST_METHOD']);
		}

		/**
		 * @return string
		 */
		public function getServeurMethode() {
			return $this->serveurVariable['REQUEST_METHOD'];
		}

		/**
		 * @return string
		 */
		public function getServeurUri() {
			return $this->serveurVariable['REQUEST_URI'];
		}

		/**
		 * @return string
		 */
		public function getServeurHttpAccept() {
			return $this->serveurVariable['HTTP_ACCEPT'];
		}

		/**
		 * @param array $serverVar
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setServeurVariable(array $serverVar) {
			if(!array_keys_exist(array('HTTP_ACCEPT', 'PHP_INPUT', 'QUERY_STRING', 'REQUEST_METHOD', 'REQUEST_URI'), $serverVar)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20300, 500);
			}

			$this->serveurVariable = $serverVar;
		}

		/**
		 * @return array
		 */
		public function getServeurDonnees() {
			return $this->serveurDonnees;
		}

		/**
		 * @param string $methode
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setServeurDonnees($methode) {
			switch(strtoupper($methode)) {
				case 'GET':
					parse_str($this->serveurVariable['QUERY_STRING'], $this->serveurDonnees);
					break;
				case 'POST':
				case 'PUT':
				case 'DELETE':
					parse_str($this->serveurVariable['PHP_INPUT'], $this->serveurDonnees);
					break;
				default:
					throw new \Serveur\Exceptions\Exceptions\MainException(20301, 405, $methode);
			}
		}
	}