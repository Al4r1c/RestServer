<?php
namespace Serveur\Reponse\Config;

use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Lib\Fichier;

class Config
{
    /**
     * @var array
     */
    private static $_clefMinimales = array('config',
        'config.default_render',
        'render');
    /**
     * @var array
     */
    private $_applicationConfiguration = array();

    /**
     * @param Fichier $fichierFramework
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function chargerConfiguration($fichierFramework)
    {
        if (!$fichierFramework instanceof Fichier) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Lib\Fichier', $fichierFramework);
        }

        try {
            $this->_applicationConfiguration = array_change_key_case($fichierFramework->chargerFichier(), CASE_UPPER);
        } catch (\Exception $fe) {
            throw new MainException(40200, 500, $fichierFramework->getCheminCompletFichier());
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

        if (false !==
            $valeur =
                rechercheValeurTableauMultidim(explode('.', strtoupper($clefConfig)), $this->_applicationConfiguration)
        ) {
            return $valeur;
        } else {
            trigger_error_app(E_USER_NOTICE, 40202, $clefConfig);

            return null;
        }
    }
}