<?php
    namespace Serveur\GestionErreurs\Exceptions;

    use Serveur\Lib\ObjetReponse;

    class MainException extends \Exception
    {
        /**
         * @var ObjetReponse
         */
        private $_objetReponseErreur;

        /**
         * @param string $code
         * @param int $codeStatus
         */
        public function __construct($code, $codeStatus)
        {
            parent::__construct('', $code);
            $this->setObjetReponseErreur($codeStatus);
            trigger_error_app(E_USER_ERROR, $code, array_slice(func_get_args(), 2));
        }

        /**
         * @return ObjetReponse
         */
        public function getObjetReponseErreur()
        {
            return $this->_objetReponseErreur;
        }

        /**
         * @param int $codeHttpErreur
         */
        public function setObjetReponseErreur($codeHttpErreur)
        {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp($codeHttpErreur);

            $this->_objetReponseErreur = $objetReponse;
        }

    }