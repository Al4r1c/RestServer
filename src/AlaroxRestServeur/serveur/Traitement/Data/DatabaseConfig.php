<?php
namespace AlaroxRestServeur\Serveur\Traitement\Data;

use AlaroxFileManager\FileManager\File;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException;

class DatabaseConfig
{
    /**
     * @var string
     */
    private $_driver;

    /**
     * @var string
     */
    private $_username;

    /**
     * @var string
     */
    private $_password;

    /**
     * @var string
     */
    private $_host;

    /**
     * @var int
     */
    private $_port;

    /**
     * @var string
     */
    private $_database;

    /**
     * @param File $fichier
     * @throws MainException
     */
    public function recupererInformationFichier($fichier)
    {
        if (!is_array($informations = $fichier->loadFile()) ||
            !array_keys_exist(array('Driver', 'User', 'Password', 'Host', 'Port', 'Database'), $informations)
        ) {
            throw new MainException(30101, 500);
        }

        $this->setDriver($informations['Driver']);
        $this->setUsername($informations['User']);
        $this->setPassword($informations['Password']);
        $this->setHost($informations['Host']);
        $this->setPort($informations['Port']);
        $this->setDatabase($informations['Database']);
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->_driver;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->_database;
    }

    /**
     * @param string $driver
     * @throws ArgumentTypeException
     */
    public function setDriver($driver)
    {
        if (!is_string($driver)) {
            throw new ArgumentTypeException(500, 'string', $driver);
        }

        $this->_driver = $driver;
    }

    /**
     * @param string $host
     * @throws ArgumentTypeException
     */
    public function setHost($host)
    {
        if (!is_string($host)) {
            throw new ArgumentTypeException(500, 'string', $host);
        }

        $this->_host = $host;
    }

    /**
     * @param int $port
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function setPort($port)
    {
        if (!is_numeric($port)) {
            throw new ArgumentTypeException(500, 'numeric', $port);
        }

        if ($port < 1 || $port > 65535) {
            throw new MainException(30100, 500, $port);
        }

        $this->_port = $port;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * @param string $database
     * @throws ArgumentTypeException
     */
    public function setDatabase($database)
    {
        if (!is_string($database)) {
            throw new ArgumentTypeException(500, 'string', $database);
        }

        $this->_database = $database;
    }
}