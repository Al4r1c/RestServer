<?php
    namespace Serveur\Exceptions\Exceptions;

    class ArgumentTypeException extends MainException {
        /**
         * @var string
         */
        private $_obtenu;

        /**
         * @param string $code
         * @param int $codeStatus
         * @param string $methode
         * @param string $attendu
         * @param mixed $typeVariable
         */
        public function __construct($code, $codeStatus, $methode, $attendu, $typeVariable) {
            if (!is_object($typeVariable)) {
                $this->setObtenu(gettype($typeVariable));
            } else {
                $this->setObtenu($this->_obtenu = get_class($typeVariable));
            }

            parent::__construct($code, $codeStatus, $methode, $attendu, $this->_obtenu);
        }

        /**
         * @param string $typeObtenu
         */
        public function setObtenu($typeObtenu) {
            $this->_obtenu = $typeObtenu;
        }
    }