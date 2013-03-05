<?php
    namespace Serveur\Exceptions;

    class ErrorManager {
        /**
         * @var \Serveur\Exceptions\Handler\ErrorHandling
         */
        private $_errorHandler;

        /**
         * @param Handler\ErrorHandling $errorHandler
         */
        public function setErrorHandler(\Serveur\Exceptions\Handler\ErrorHandling $errorHandler) {
            $this->_errorHandler = $errorHandler;
        }

        public function setHandlers() {
            $this->_errorHandler->setHandlers();
        }

        /**
         * @return \string[]
         */
        public function getErreurs() {
            return $this->_errorHandler->getErreurs();
        }
    }