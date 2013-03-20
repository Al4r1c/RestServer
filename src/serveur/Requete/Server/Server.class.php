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
     * @var array
     */
    private $_serveurDonnees;

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
        $this->setServeurDonnees($varServeur['REQUEST_METHOD']);
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
     * @return array
     */
    public function getServeurDonnees()
    {
        return $this->_serveurDonnees;
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
                'PHP_INPUT',
                'QUERY_STRING',
                'REMOTE_ADDR',
                'REQUEST_METHOD',
                'REQUEST_TIME',
                'REQUEST_URI'), $serverVar
        )
        ) {
            throw new MainException(20100, 500);
        }

        $this->_serveurVariables = $serverVar;
    }

    /**
     * @param string $methode
     * @throws MainException
     */
    public function setServeurDonnees($methode)
    {
        switch (strtoupper($methode)) {
            case 'GET':
                parse_str($this->_serveurVariables['QUERY_STRING'], $this->_serveurDonnees);
                break;
            case 'POST':
            case 'PUT':
                parse_str($this->_serveurVariables['PHP_INPUT'], $this->_serveurDonnees);
                break;
            case 'DELETE':
                $this->_serveurDonnees = array();
                break;
            default:
                throw new MainException(20101, 405, $methode);
        }
    }
}