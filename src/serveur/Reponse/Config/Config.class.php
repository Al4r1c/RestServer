<?php
namespace Serveur\Reponse\Config;

use AlaroxFileManager\FileManager\File;
use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;

class Config
{
    /**
     * @var array
     */
    private static $_clefMinimales = array('config',
        'render');
    /**
     * @var array
     */
    private $_applicationConfiguration = array();

    /**
     * @param File $fichierFramework
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function chargerConfiguration($fichierFramework)
    {
        if (!$fichierFramework instanceof File) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, '\AlaroxFileManager\File', $fichierFramework);
        }

        try {
            $this->_applicationConfiguration = $fichierFramework->loadFile();
            array_change_key_case_recursive($this->_applicationConfiguration, CASE_UPPER);
        } catch (\Exception $fe) {
            throw new MainException(40200, 500, $fichierFramework->getPathToFile());
        }

        $this->validerFichierConfiguration();
    }

    /**
     * @throws MainException
     */
    private function validerFichierConfiguration()
    {
        foreach (self::$_clefMinimales as $uneClefQuiDoitExister) {
            if (is_null($this->getConfigValeur($uneClefQuiDoitExister))) {
                throw new MainException(40201, 500, $uneClefQuiDoitExister);
            }
        }
    }

    /**
     * @param string $clefConfig
     * @throws ArgumentTypeException
     * @return array|bool|null
     */
    public function getConfigValeur($clefConfig)
    {
        if (!is_string($clefConfig)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $clefConfig);
        }

        if (!is_null($valeur = array_key_multi_get($clefConfig, $this->_applicationConfiguration, true))
        ) {
            return $valeur;
        } else {
            trigger_error_app(E_USER_NOTICE, 40202, array($clefConfig));

            return null;
        }
    }
}