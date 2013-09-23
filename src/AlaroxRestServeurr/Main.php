<?php
namespace AlaroxRestServeur;

use AlaroxRestServeur\Conteneur\Conteneur;
use AlaroxRestServeur\Logging\LoggingFactory;
use AlaroxRestServeur\Serveur\MainApplication;

include_once(__DIR__ . '/../../config/bootstrap.php');
include_once(BASE_PATH . '/functions/functions.php');

class Main
{
    /**
     * @var MainApplication
     */
    private $_main;

    /**
     * @var array
     */
    private static $_clefsMinimales = array('configMain', 'configDatabase', 'authorizationFile');

    /**
     * @param array $arrayConfig
     * @param array $driverList
     * @throws \Exception
     */
    public function __construct($arrayConfig, $driverList)
    {
        foreach (self::$_clefsMinimales as $uneConfigObligatoire) {
            if (!array_key_exists($uneConfigObligatoire, $arrayConfig)) {
                throw new \Exception(sprintf('Missing configuration key %s', $uneConfigObligatoire));
            }
        }

        $conteneur = new Conteneur();
        $conteneur->buildConteneur($arrayConfig + array('logFolder' => '.'), $driverList);

        $this->_main = new MainApplication($conteneur);
        $this->_main->ajouterObserveur(LoggingFactory::getLogger('logger', $arrayConfig['logFolder']));
        $this->_main->setHandlers();
    }

    public function output()
    {
        echo $this->_main->run();
    }
}