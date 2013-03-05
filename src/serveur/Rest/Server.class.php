<?php
    namespace Serveur\Rest;

    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

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
         * @throws ArgumentTypeException
         */
        public function setVarServeur($varServeur) {
            if (!is_array($varServeur)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $varServeur);
            }

            $this->setServeurVariable($varServeur);
            $this->setServeurDonnees($varServeur['REQUEST_METHOD']);
        }

        /**
         * @return string
         */
        public function getServeurHttpAccept() {
            return $this->serveurVariable['HTTP_ACCEPT'];
        }

        /**
         * @return string
         */
        public function getRemoteIp() {
            return $this->serveurVariable['REMOTE_ADDR'];
        }

        /**
         * @return string
         */
        public function getServeurMethode() {
            return $this->serveurVariable['REQUEST_METHOD'];
        }

        /**
         * @return int
         */
        public function getRequestTime() {
            return $this->serveurVariable['REQUEST_TIME'];
        }

        /**
         * @return string
         */
        public function getServeurUri() {
            return $this->serveurVariable['REQUEST_URI'];
        }

        /**
         * @param array $serverVar
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setServeurVariable($serverVar) {
            if (!is_array($serverVar)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $serverVar);
            }

            if (!array_keys_exist(array('HTTP_ACCEPT', 'PHP_INPUT', 'QUERY_STRING', 'REMOTE_ADDR', 'REQUEST_METHOD', 'REQUEST_TIME', 'REQUEST_URI'), $serverVar)) {
                throw new MainException(20300, 500);
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
         * @throws MainException
         */
        public function setServeurDonnees($methode) {
            switch (strtoupper($methode)) {
                case 'GET':
                    parse_str($this->serveurVariable['QUERY_STRING'], $this->serveurDonnees);
                    break;
                case 'POST':
                case 'PUT':
                case 'DELETE':
                    parse_str($this->serveurVariable['PHP_INPUT'], $this->serveurDonnees);
                    break;
                default:
                    throw new MainException(20301, 405, $methode);
            }
        }
    }