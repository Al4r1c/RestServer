<?php
    namespace Serveur\Exceptions;

    class ErrorManager {
        /**
         * @var \Serveur\Exceptions\Handler\ErrorHandler
         */
        private $_errorHandler;

        /**
         * @param \Serveur\Exceptions\Handler\ErrorHandler $errorHandler
         */
        public function setErrorHandler(\Serveur\Exceptions\Handler\ErrorHandler $errorHandler) {
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