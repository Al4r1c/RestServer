<?php
    namespace Serveur\GestionErreurs;

    class ErreurManager
    {
        /**
         * @var \Serveur\GestionErreurs\Handler\ErreurHandler
         */
        private $_errorHandler;

        /**
         * @param \Serveur\GestionErreurs\Handler\ErreurHandler $errorHandler
         */
        public function setErrorHandler($errorHandler)
        {
            $this->_errorHandler = $errorHandler;
        }

        public function setHandlers()
        {
            $this->_errorHandler->setHandlers();
        }

        /**
         * @param \Logging\Displayer\AbstractDisplayer $logger
         */
        public function ajouterObserveur($logger)
        {
            $this->_errorHandler->ajouterUnLogger($logger);
        }
    }