<?php
	namespace Serveur\Rest;

	use Serveur\Lib\TypeDetector;

	class RestRequete {
		/**
		 * @var string
		 */
		private $methode = 'GET';

		/**
		 * @var string[]
		 */
		private $formatsDemandes;

		/**
		 * @var string[]
		 */
		private $dataUri;

		/**
		 * @var string[]
		 */
		private $parametres;


		/**
		 * @param Server $server
		 */
		public function setServer(Server $server) {
			$this->setMethode($server->getServeurMethode());
			$this->setFormat($server->getServeurHttpAccept());
			$this->setVariableUri($server->getServeurUri());
			$this->setParametres($server->getServeurDonnees());
		}

		/**
		 * @return string
		 */
		public function getMethode() {
			return $this->methode;
		}

		/**
		 * @return string[]
		 */
		public function getFormatsDemandes() {
			return $this->formatsDemandes;
		}

		/**
		 * @return string[]
		 */
		public function getUriVariables() {
			return $this->dataUri;
		}

		/**
		 * @return string[]
		 */
		public function getParametres() {
			return $this->parametres;
		}

		/**
		 * @param string $method
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setMethode($method) {
			$method = strtoupper(trim($method));

			if(!in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20000, 400, $method);
			}

			$this->methode = $method;
		}

		/**
		 * @param string $format
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setFormat($format) {
			$typeDetector = new TypeDetector(\Serveur\Utils\Constante::chargerConfig('mimes'));
			$formatsTrouves = $typeDetector->extraireMimesTypeHeader($format);

			if(isNull($formatsTrouves)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20001, 400);
			}

			$this->formatsDemandes = $formatsTrouves;
		}

		/**
		 * @param string $uri
		 */
		public function setVariableUri($uri) {
			if(!isNull($uri)) {
				$this->dataUri = array_map('rawurlencode', explode('/', trim(preg_replace('%([^:])([/]{2,})%', '\\1/', $uri), '/')));
			}
		}

		/**
		 * @param string $donnee
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setParametres($donnee) {
			if(!is_array($donnee)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20002, 400);
			}

			$this->parametres = $donnee;
		}
	}