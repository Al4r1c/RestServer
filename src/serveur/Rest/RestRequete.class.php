<?php
    namespace Serveur\Rest;

    use Serveur\Lib\TypeDetector;
    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

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
         * @var string
         */
        private $ip;

        /**
         * @var \DateTime
         */
        private $dateRequete;

        /**
         * @param Server $server
         * @throws ArgumentTypeException
         */
        public function setServer($server) {
            if (!$server instanceof Server) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\Server', $server);
            }

            $this->setMethode($server->getServeurMethode());
            $this->setFormat($server->getServeurHttpAccept());
            $this->setVariableUri($server->getServeurUri());
            $this->setParametres($server->getServeurDonnees());
            $this->setIp($server->getRemoteIp());
            $this->setDateRequete($server->getRequestTime());
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
         * @return \DateTime
         */
        public function getDateRequete() {
            return $this->dateRequete;
        }

        /**
         * @return string
         */
        public function getIp() {
            return $this->ip;
        }

        /**
         * @param string $method
         * @throws MainException
         */
        public function setMethode($method) {
            $method = strtoupper(trim($method));

            if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
                throw new MainException(20000, 400, $method);
            }

            $this->methode = $method;
        }

        /**
         * @param string $format
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setFormat($format) {
            if (!is_string($format)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $format);
            }

            $typeDetector = new TypeDetector(\Serveur\Utils\Constante::chargerConfig('mimes'));
            $formatsTrouves = $typeDetector->extraireMimesTypeHeader($format);

            if (isNull($formatsTrouves)) {
                throw new MainException(20001, 400);
            }

            $this->formatsDemandes = $formatsTrouves;
        }

        /**
         * @param string $uri
         * @throws ArgumentTypeException
         */
        public function setVariableUri($uri) {
            if (!isNull($uri)) {
                if (!is_string($uri)) {
                    throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $uri);
                }

                if (($pos = strpos($uri, '?')) !== false) {
                    $uri = substr($uri, 0, $pos);
                }

                $this->dataUri = array_map('rawurlencode', explode('/', trim(preg_replace('%([^:])([/]{2,})%', '\\1/', $uri), '/')));
            }
        }

        /**
         * @param string $donnee
         * @throws ArgumentTypeException
         */
        public function setParametres($donnee) {
            if (!is_array($donnee)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'array', $donnee);
            }

            $this->parametres = $donnee;
        }

        /**
         * @param int $dateRequeteTimestamp
         * @throws ArgumentTypeException
         * @internal param \DateTime $dateRequete
         */
        public function setDateRequete($dateRequeteTimestamp) {
            if (!is_int($dateRequeteTimestamp)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'int', $dateRequeteTimestamp);
            }

            $datetime = new \DateTime();
            $datetime->setTimestamp($dateRequeteTimestamp);
            $this->dateRequete = $datetime;
        }

        /**
         * @param string $ip
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setIp($ip) {
            if (!is_string($ip)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $ip);
            }

            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new MainException(20002, 400);
            }

            $this->ip = $ip;
        }
    }