<?php
	namespace Serveur\Rest;

	use Serveur\Utils\TypeDetector;
	use Serveur\Exceptions\Exceptions\RestRequeteException;

	class RestRequete {
		private $methode;
		private $formatsDemandes;
		private $dataUri;
		private $parametres;

		public function setServer(Server $server) {
			$this->setMethode($server->getServeurMethode());
			$this->setFormat($server->getServeurHttpAccept());
			$this->setVariableUri($server->getServeurUri());
			$this->setParametres($server->getServeurDonnees());
		}

		public function getMethode() {
			return $this->methode;
		}

		public function getFormatsDemandes() {
			return $this->formatsDemandes;
		}

		public function getUriVariables() {
			return $this->dataUri;
		}

		public function getParametres() {
			return $this->parametres;
		}

		public function setMethode($method) {
			$method = strtoupper(trim($method));

			if(!in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
				throw new RestRequeteException(20000, 400, $method);
			}

			$this->methode = $method;
		}

		public function setFormat($format) {
			$typeDetector = new TypeDetector(\Serveur\Utils\Constante::chargerConfig('mimes'));
			$formatsTrouves = $typeDetector->extraireMimesTypeHeader($format);

			if (isNull($formatsTrouves)) {
				throw new RestRequeteException(20001, 400);
			}

			$this->formatsDemandes = $formatsTrouves;
		}

		public function setVariableUri($uri) {
			if(!isNull($uri)) {
				$this->dataUri = array_map('rawurlencode', explode('/', trim(preg_replace('%([^:])([/]{2,})%', '\\1/', $uri), '/')));
			}
		}

		public function setParametres($donnee) {
			if (!is_array($donnee)) {
				throw new RestRequeteException(20002, 400);
			}

			$this->parametres = $donnee;
		}
	}