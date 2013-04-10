<?php
namespace Serveur\Requete\Server;

use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;

class Server
{
    /**
     * @var array
     */
    private $_serveurVariables;

    /**
     * @param array $varServeur
     * @throws ArgumentTypeException
     */
    public function setVarServeur($varServeur)
    {
        if (!is_array($varServeur)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $varServeur);
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
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $serverVar);
        }

        if (!array_keys_exist(
            array('HTTP_ACCEPT',
                'CONTENT_TYPE',
                'HTTP_DATE',
                'PHP_INPUT',
                'QUERY_STRING',
                'REMOTE_ADDR',
                'REQUEST_METHOD',
                'REQUEST_URI',
                'REDIRECT_HTTP_AUTHORIZATION'), $serverVar
        )
        ) {
            throw new MainException(20100, 500);
        }

        $this->_serveurVariables = $serverVar;
    }
}