<?php
namespace AlaroxRestServeur\Serveur\Traitement\Authorization;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

class Authorization
{
    /**
     * @var string
     */
    private $_entityId;

    /**
     * @var string
     */
    private $_clefPrivee;

    public function getEntityId()
    {
        return $this->_entityId;
    }

    public function getClefPrivee()
    {
        return $this->_clefPrivee;
    }

    public function setEntityId($idEntity)
    {
        if (!is_string($idEntity)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $idEntity);
        }

        $this->_entityId = $idEntity;
    }


    public function setClefPrivee($clefPrivee)
    {
        if (!is_string($clefPrivee)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $clefPrivee);
        }

        $this->_clefPrivee = $clefPrivee;
    }
}