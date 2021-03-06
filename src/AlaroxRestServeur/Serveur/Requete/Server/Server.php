<?php
namespace AlaroxRestServeur\Serveur\Requete\Server;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException;

class Server
{
    /**
     * @var array
     */
    private $_serveurVariables;

    /**
     * @var array
     */
    private static $_tabClefsMinimales = array('HTTP_ACCEPT',
        'HTTP_DATE',
        'PHP_INPUT',
        'QUERY_STRING',
        'REMOTE_ADDR',
        'REQUEST_METHOD',
        'REQUEST_URI',
        'REDIRECT_HTTP_AUTHORIZATION');

    /**
     * @param array $varServeur
     * @throws ArgumentTypeException
     */
    public function setVarServeur($varServeur)
    {
        if (!is_array($varServeur)) {
            throw new ArgumentTypeException(500, 'array', $varServeur);
        }

        $this->setServeurVariables($varServeur);
    }

    /**
     * @return array
     */
    public function getServeurVariables()
    {
        return $this->_serveurVariables;
    }

    /**
     * @param string $clef
     * @return string|null
     */
    public function getUneVariableServeur($clef)
    {
        if (array_key_exists($clef, $this->_serveurVariables)) {
            return $this->_serveurVariables[$clef];
        } else {
            return null;
        }
    }

    /**
     * @param array $serverVar
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function setServeurVariables($serverVar)
    {
        if (!is_array($serverVar)) {
            throw new ArgumentTypeException(500, 'array', $serverVar);
        }

        foreach (self::$_tabClefsMinimales as $uneClefMini) {
            if (!array_key_exists($uneClefMini, $serverVar)) {
                throw new MainException(20100, 500, $uneClefMini);
            }
        }

        $this->_serveurVariables = $serverVar;
    }
}