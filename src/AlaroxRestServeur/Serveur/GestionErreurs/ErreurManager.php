<?php
namespace AlaroxRestServeur\Serveur\GestionErreurs;

use AlaroxRestServeur\Logging\Displayer\AbstractDisplayer;
use AlaroxRestServeur\Serveur\GestionErreurs\Handler\ErreurHandler;

class ErreurManager
{
    /**
     * @var ErreurHandler
     */
    private $_errorHandler;

    /**
     * @param ErreurHandler $errorHandler
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
     * @param AbstractDisplayer $logger
     */
    public function ajouterObserveur($logger)
    {
        $this->_errorHandler->ajouterUnLogger($logger);
    }
}