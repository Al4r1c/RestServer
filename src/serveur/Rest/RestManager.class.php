<?php
    namespace Serveur\Rest;

    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class RestManager {
        /**
         * @var \Serveur\Rest\RestRequete
         */
        private $_restRequest;

        /**
         * @var \Serveur\Rest\RestReponse
         */
        private $_restResponse;

        /**
         * @return \Serveur\Rest\RestRequete
         */
        public function getRestRequest() {
            return $this->_restRequest;
        }

        /**
         * @return \Serveur\Rest\RestReponse
         */
        public function getRestResponse() {
            return $this->_restResponse;
        }

        /**
         * @param \Serveur\Rest\RestRequete $restRequestObject
         * @throws ArgumentTypeException
         */
        public function setRequete($restRequestObject) {
            if (!$restRequestObject instanceof \Serveur\Rest\RestRequete) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\RestRequete', $restRequestObject);
            }

            $this->_restRequest = $restRequestObject;
        }

        /**
         * @param \Serveur\Rest\RestReponse $restReponseObject
         * @throws ArgumentTypeException
         */
        public function setReponse($restReponseObject) {
            if (!$restReponseObject instanceof \Serveur\Rest\RestReponse) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\RestReponse', $restReponseObject);
            }

            $this->_restResponse = $restReponseObject;
        }

        /**
         * @param int $clef
         * @throws ArgumentTypeException
         * @return mixed|null
         */
        public function getUriVariable($clef) {
            if (!is_int($clef)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'int', $clef);
            }

            if (array_key_exists($clef, $tabVarUri = $this->_restRequest->getUriVariables())) {
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
            return $this->_restRequest->getParametres();
        }

        /**
         * @param string $clef
         * @throws ArgumentTypeException
         * @return mixed|null
         */
        public function getParametre($clef) {
            if (!is_string($clef)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $clef);
            }

            if (array_key_exists($clef, $tabParam = $this->_restRequest->getParametres())) {
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
            $this->_restResponse->setStatus($status);
            $this->_restResponse->setContenu($contenu);
        }

        /**
         * @return string
         */
        public function fabriquerReponse() {
            return $this->_restResponse->fabriquerReponse($this->_restRequest->getFormatsDemandes());
        }
    }