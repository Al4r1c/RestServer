<?php
    namespace Serveur\Exceptions\Exceptions;

    class ArgumentTypeException extends MainException {
        /**
         * @param string $code
         * @param int $codeStatus
         * @param string $methode
         * @param string $attendu
         * @param string $typeVariable
         */
        public function __construct($code, $codeStatus, $methode, $attendu, $typeVariable) {
            if(!is_object($typeVariable)) {
                $obtenu = gettype($typeVariable);
            } else {
                $obtenu = get_class($typeVariable);
            }

            parent::__construct($code, $codeStatus, $methode, $attendu, $obtenu);
        }
    }