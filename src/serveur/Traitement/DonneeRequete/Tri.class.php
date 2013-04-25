<?php
namespace Serveur\Traitement\DonneeRequete;

class Tri
{
    /**
     * @var string
     */
    private $_typeTri;

    /**
     * @var string
     */
    private $_valeur;

    /**
     * @return string
     */
    public function getValeur()
    {
        return $this->_valeur;
    }

    /**
     * @return string
     */
    public function getTypeTri()
    {
        return $this->_typeTri;
    }

    /**
     * @param string $type
     */
    public function setTypeTri($type)
    {
        $this->_typeTri = $type;
    }

    /**
     * @param string $valeur
     */
    public function setValeur($valeur)
    {
        $this->_valeur = $valeur;
    }
}