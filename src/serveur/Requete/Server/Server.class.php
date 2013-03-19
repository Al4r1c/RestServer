<?php
namespace Serveur\Requete\Server;

use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;

class Server
{
    /**
     * @var array
     */
    private $_serveurVariable;

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

        $this->setServeurVariable($varServeur);
        $this->setServeurDonnees($varServeur['REQUEST_METHOD']);
    }

    /**
     * @return string
     */
    public function getServeurHttpAccept()
    {
        return $this->_serveurVariable['HTTP_ACCEPT'];
    }

    /**
     * @return string
     */
    public function getRemoteIp()
    {
        return $this->_serveurVariable['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    public function getServeurMethode()
    {
        return $this->_serveurVariable['REQUEST_METHOD'];
    }

    /**
     * @return int
     */
    public function getRequestTime()
    {
        return $this->_serveurVariable['REQUEST_TIME'];
    }

    /**
     * @return string
     */
    public function getServeurUri()
    {
        return $this->_serveurVariable['REQUEST_URI'];
    }

    /**
     * @param array $serverVar
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function setServeurVariable($serverVar)
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

        $this->_serveurVariable = $serverVar;
    }

    /**
     * @return array
     */
    public function getServeurDonnees()
    {
        return $this->_serveurDonnees;
    }

    /**
     * @param string $methode
     * @throws MainException
     */
    public function setServeurDonnees($methode)
    {
        switch (strtoupper($methode)) {
            case 'GET':
                parse_str($this->_serveurVariable['QUERY_STRING'], $this->_serveurDonnees);
                break;
            case 'POST':
            case 'PUT':
                parse_str($this->_serveurVariable['PHP_INPUT'], $this->_serveurDonnees);
                break;
            case 'DELETE':
                $this->_serveurDonnees = array();
                break;
            default:
                throw new MainException(20101, 405, $methode);
        }
    }
}