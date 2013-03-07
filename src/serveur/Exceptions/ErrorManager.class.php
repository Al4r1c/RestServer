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
         * @param \Logging\Displayer\AbstractDisplayer $logger
         */
        public function ajouterObserveur($logger) {
            $this->_errorHandler->ajouterUnLogger($logger);
        }
    }