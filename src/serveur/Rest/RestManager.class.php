<?php
	namespace Serveur\Rest;

	class RestManager {

		/** @var \Serveur\Rest\RestRequete */
		private $restRequest;
		/** @var \Serveur\Rest\RestReponse */
		private $restResponse;

		public function setRequete(\Serveur\Rest\RestRequete $restRequestObject) {
			$this->restRequest = $restRequestObject;
		}

		public function setReponse(\Serveur\Rest\RestReponse $restReponseObject) {
			$this->restResponse = $restReponseObject;
		}

		public function recupererReponse() {
			return $this->restResponse->getContenu();
		}

		public function fabriquerReponse() {
			$this->restResponse->fabriquerReponse($this->restRequest->getFormatsDemandes());
		}

		public function getUriVariable($clef) {
			if (array_key_exists($clef, $tabVarUri = $this->restRequest->getUriVariables())) {
				return $tabVarUri[$clef];
			} else {
				return null;
			}
		}

		public function getParametres() {
			return $this->restRequest->getParametres();
		}

		public function getParametre($clef) {
			if (array_key_exists($clef, $tabParam = $this->restRequest->getParametres())) {
				return $tabParam[$clef];
			} else {
				return null;
			}
		}

		public function setVariablesReponse($status, $contenu = '') {
			$this->restResponse->setStatus($status);
			$this->restResponse->setContenu($contenu);
		}
	}