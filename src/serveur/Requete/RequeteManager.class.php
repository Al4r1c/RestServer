<?php
    namespace Serveur\Requete;

    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
    use Serveur\GestionErreurs\Exceptions\MainException;
    use Serveur\Lib\TypeDetector;
    use Serveur\Requete\Server\Server;
    use Serveur\Utils\Constante;

    class RequeteManager
    {
        /**
         * @var string
         */
        private $_methode = 'GET';

        /**
         * @var string[]
         */
        private $_formatsDemandes;

        /**
         * @var string[]
         */
        private $_dataUri;

        /**
         * @var string[]
         */
        private $_parametres = array();

        /**
         * @var string
         */
        private $_ip;

        /**
         * @var \DateTime
         */
        private $_dateRequete;

        /**
         * @param Server $server
         * @throws ArgumentTypeException
         */
        public function parseServer($server)
        {
            if (!$server instanceof Server) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Requete\Server\Server', $server);
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
        public function getMethode()
        {
            return $this->_methode;
        }

        /**
         * @return string[]
         */
        public function getFormatsDemandes()
        {
            return $this->_formatsDemandes;
        }

        /**
         * @return string[]
         */
        public function getUriVariables()
        {
            return $this->_dataUri;
        }

        public function getUriVariable($clef)
        {
            if (array_key_exists($clef, $this->_dataUri)) {
                return $this->_dataUri[$clef];
            } else {
                return null;
            }
        }

        /**
         * @return string[]
         */
        public function getParametres()
        {
            return $this->_parametres;
        }

        /**
         * @return \DateTime
         */
        public function getDateRequete()
        {
            return $this->_dateRequete;
        }

        /**
         * @return string
         */
        public function getIp()
        {
            return $this->_ip;
        }

        /**
         * @param string $method
         * @throws MainException
         */
        public function setMethode($method)
        {
            $method = strtoupper(trim($method));

            if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
                throw new MainException(20000, 400, $method);
            }

            $this->_methode = $method;
        }

        /**
         * @param string $format
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setFormat($format)
        {
            if (!is_string($format)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $format);
            }

            $typeDetector = new TypeDetector(Constante::chargerConfig('mimes'));
            $formatsTrouves = $typeDetector->extraireMimesTypeHeader($format);

            if (isNull($formatsTrouves)) {
                throw new MainException(20001, 400);
            }

            $this->_formatsDemandes = $formatsTrouves;
        }

        /**
         * @param string $uri
         * @throws ArgumentTypeException
         */
        public function setVariableUri($uri)
        {
            if (!isNull($uri)) {
                if (!is_string($uri)) {
                    throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $uri);
                }

                if (($pos = strpos($uri, '?')) !== false) {
                    $uri = substr($uri, 0, $pos);
                }

                $this->_dataUri =
                    array_map('rawurlencode', explode('/', trim(preg_replace('%([^:])([/]{2,})%', '\\1/', $uri), '/')));
            }
        }

        /**
         * @param array $donnee
         * @throws ArgumentTypeException
         */
        public function setParametres($donnee)
        {
            if (!is_array($donnee)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'array', $donnee);
            }

            $this->_parametres = $donnee;
        }

        /**
         * @param int $dateRequeteTimestamp
         * @throws ArgumentTypeException
         * @internal param \DateTime $dateRequete
         */
        public function setDateRequete($dateRequeteTimestamp)
        {
            if (!is_int($dateRequeteTimestamp)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'int', $dateRequeteTimestamp);
            }

            $datetime = new \DateTime();
            $datetime->setTimestamp($dateRequeteTimestamp);
            $this->_dateRequete = $datetime;
        }

        /**
         * @param string $ip
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setIp($ip)
        {
            if (!is_string($ip)) {
                throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $ip);
            }

            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new MainException(20002, 400);
            }

            $this->_ip = $ip;
        }
    }