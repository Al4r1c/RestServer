<?php
namespace AlaroxRestServeur;

use AlaroxRestServeur\Conteneur\Conteneur;
use AlaroxRestServeur\Logging\LoggingFactory;
use AlaroxRestServeur\Serveur\MainApplication;

class Main
{
    /**
     * @var MainApplication
     */
    private $_main;

    public function __construct()
    {
        $main = new MainApplication(new Conteneur());
        $main->ajouterObserveur(LoggingFactory::getLogger('logger'));
        $main->setHandlers();
    }

    public function output()
    {
        echo $this->_main->run();
    }
}