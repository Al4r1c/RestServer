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
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 */
		public function setRequete($restRequestObject) {
			if(!$restRequestObject instanceof \Serveur\Rest\RestRequete) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\RestRequete', $restRequestObject);
			}

			$this->restRequest = $restRequestObject;
		}

		/**
		 * @param \Serveur\Rest\RestReponse $restReponseObject
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 */
		public function setReponse($restReponseObject) {
			if(!$restReponseObject instanceof \Serveur\Rest\RestReponse) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\RestReponse', $restReponseObject);
			}

			$this->restResponse = $restReponseObject;
		}

		/**
		 * @param int $clef
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @return mixed|null
		 */
		public function getUriVariable($clef) {
			if(!is_int($clef)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'int', $clef);
			}

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
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @return mixed|null
		 */
		public function getParametre($clef) {
			if(!is_string($clef)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $clef);
			}

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