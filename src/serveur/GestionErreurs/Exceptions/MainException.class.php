<?php
    namespace Serveur\GestionErreurs\Exceptions;

    use Serveur\Utils\Tools;

    class MainException extends \Exception
    {
        /**
         * @var int
         */
        private $_codeRetourHttp = 500;

        /**
         * @param string $code
         * @param int $codeStatus
         */
        public function __construct($code, $codeStatus)
        {
            parent::__construct('', $code);
            $this->setStatus($codeStatus);
            trigger_error_app(E_USER_ERROR, $code, array_slice(func_get_args(), 2));
        }

        /**
         * @return int
         */
        public function getStatus()
        {
            return $this->_codeRetourHttp;
        }

        /**
         * @param int $codeHttp
         * @throws \Exception
         */
        public function setStatus($codeHttp)
        {
            if (!is_int($codeHttp)) {
                throw new \Exception('Invalid argument type, int required');
            }

            if (Tools::isValideHttpCode($codeHttp)) {
                $this->_codeRetourHttp = $codeHttp;
            }
        }
    }