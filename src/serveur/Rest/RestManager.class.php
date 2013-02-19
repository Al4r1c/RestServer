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

		public function getUriVariable($clef) {
			if (array_key_exists($clef, $tabVarUri = $this->restRequest->getUriVariables())) {
				return $tabVarUri[$clef];
			} else {
				trigger_notice_apps(20200, $clef);
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
				trigger_notice_apps(20201, $clef);
				return null;
			}
		}

		public function setVariablesReponse($status, $contenu = '') {
			$this->restResponse->setStatus($status);
			$this->restResponse->setContenu($contenu);
		}

		public function fabriquerReponse() {
			return $this->restResponse->fabriquerReponse($this->restRequest->getFormatsDemandes());
		}
	}