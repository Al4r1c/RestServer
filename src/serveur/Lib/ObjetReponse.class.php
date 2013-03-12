<?php
    namespace Serveur\Lib;

    use Serveur\Utils\Tools;
    use Serveur\Utils\Constante;
    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
    use Serveur\GestionErreurs\Exceptions\MainException;

    class ObjetReponse
    {
        /**
         * @var int
         */
        private $_statusHttp;

        /**
         * @var array
         */
        private $_donneesReponse;

        public function __construct($statusHttp = 200, $donneesReponse = array())
        {
            $this->setStatusHttp($statusHttp);
            $this->setDonneesReponse($donneesReponse);
        }

        /**
         * @return int
         */
        public function getStatusHttp()
        {
            return $this->_statusHttp;
        }

        public function getDonneesReponse()
        {
            return $this->_donneesReponse;
        }

        /**
         * @param int $statusHttp
         * @throws \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @throws \Serveur\GestionErreurs\Exceptions\MainException
         */
        public function setStatusHttp($statusHttp)
        {
            if (!is_int($statusHttp)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'int', $statusHttp);
            }

            if (!Tools::isValideHttpCode($statusHttp)) {
                throw new MainException(10300, 500, $statusHttp);
            }

            $this->_statusHttp = $statusHttp;
        }

        /**
         * @param array $donneesReponse
         * @throws \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         */
        public function setDonneesReponse($donneesReponse)
        {
            if (!is_array($donneesReponse)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $donneesReponse);
            }

            $this->_donneesReponse = $donneesReponse;
        }

        public function setErreurHttp($statusHttp)
        {
            $infoHttpCode = Constante::chargerConfig('httpcode')[$statusHttp];

            $this->setStatusHttp($statusHttp);
            $this->setDonneesReponse(
                array('Code' => $statusHttp, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1])
            );
        }
    }