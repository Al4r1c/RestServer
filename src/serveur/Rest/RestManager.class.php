<?php
	namespace Serveur\Rest;

	class RestManager {
		/**
		 * @var \Serveur\Rest\RestRequete
		 */
		private $restRequest;

		/**
		 * @var \Serveur\Rest\RestReponse
		 */
		private $restResponse;

		/**
		 * @param \Serveur\Rest\RestRequete $restRequestObject
		 */
		public function setRequete(\Serveur\Rest\RestRequete $restRequestObject) {
			$this->restRequest = $restRequestObject;
		}

		/**
		 * @param \Serveur\Rest\RestReponse $restReponseObject
		 */
		public function setReponse(\Serveur\Rest\RestReponse $restReponseObject) {
			$this->restResponse = $restReponseObject;
		}

		/**
		 * @param string $clef
		 * @return mixed|null
		 */
		public function getUriVariable($clef) {
			if(array_key_exists($clef, $tabVarUri = $this->restRequest->getUriVariables())) {
				return $tabVarUri[$clef];
			} else {
				trigger_error_app(E_USER_NOTICE, 20200, $clef);

				return null;
			}
		}

		/**
		 * @return array
		 */
		public function getParametres() {
			return $this->restRequest->getParametres();
		}

		/**
		 * @param string $clef
		 * @return mixed|null
		 */
		public function getParametre($clef) {
			if(array_key_exists($clef, $tabParam = $this->restRequest->getParametres())) {
				return $tabParam[$clef];
			} else {
				trigger_error_app(E_USER_NOTICE, 20201, $clef);

				return null;
			}
		}

		/**
		 * @param int $status
		 * @param string $contenu
		 */
		public function setVariablesReponse($status, $contenu = '') {
			$this->restResponse->setStatus($status);
			$this->restResponse->setContenu($contenu);
		}

		/**
		 * @return string
		 */
		public function fabriquerReponse() {
			return $this->restResponse->fabriquerReponse($this->restRequest->getFormatsDemandes());
		}
	}