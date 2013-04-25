<?php
namespace Serveur\Traitement\DonneeRequete;

use Serveur\GestionErreurs\Exceptions\MainException;

class Operateur
{
    /**
     * @var string
     */
    private $_type = 'eq';

    /**
     * @var array
     */
    static private $_motsClef = array('gt', 'gte', 'eq', 'eqs', 'lte', 'lt', 'like');

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $types
     * @throws MainException
     */
    public function setType($types)
    {
        if (!in_array($types, self::$_motsClef)) {
            throw new MainException(30300, 500);
        }

        $this->_type = $types;
    }
}