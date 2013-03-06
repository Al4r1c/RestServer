<?php
    namespace Serveur\Exceptions;

    class ErrorManager {
        /**
         * @var \Serveur\Exceptions\Handler\ErreurHandler
         */
        private $_errorHandler;

        /**
         * @param \Serveur\Exceptions\Handler\ErreurHandler $errorHandler
         */
        public function setErrorHandler($errorHandler) {
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