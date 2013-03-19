<?php
namespace Serveur\Traitement\Data;

abstract class AbstractDatabase implements IDatabaseActions
{
    /**
     * @var resource|object
     */
    private $_connection;

    /**
     * @var string
     */
    private $_nomTable;

    /**
     * @return resource|object
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return string
     */
    public function getNomTable()
    {
        return $this->_nomTable;
    }

    /**
     * @param resource|object $connection
     */
    public function setConnection($connection)
    {
        $this->_connection = $connection;
    }

    /**
     * @param string $nomTable
     */
    public function setNomTable($nomTable)
    {
        $this->_nomTable = $nomTable;
    }

    public function __destruct()
    {
        $this->fermerConnection($this->_connection);
    }
}