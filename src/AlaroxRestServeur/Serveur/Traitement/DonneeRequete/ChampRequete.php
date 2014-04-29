<?php
namespace AlaroxRestServeur\Serveur\Traitement\DonneeRequete;

class ChampRequete
{
    /**
     * @var string
     */
    private $_champ;

    /**
     * @var string|array
     */
    private $_valeurs;

    /**
     * @var Operateur
     */
    private $_operateur;

    public function __construct()
    {
        $this->_operateur = new Operateur();
    }

    /**
     * @return string
     */
    public function getChamp()
    {
        return $this->_champ;
    }

    /**
     * @return Operateur
     */
    public function getOperateur()
    {
        return $this->_operateur;
    }

    /**
     * @return array|string
     */
    public function getValeurs()
    {
        return $this->_valeurs;
    }

    /**
     * @param string $champ
     */
    public function setChamp($champ)
    {
        $this->_champ = $champ;
    }

    /**
     * @param Operateur $unOperateur
     */
    public function setOperateur($unOperateur)
    {
        $this->_operateur = $unOperateur;
    }

    /**
     * @param array|string $valeurs
     */
    public function setValeurs($valeurs)
    {
        $this->_valeurs = $valeurs;
    }
}