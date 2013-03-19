<?php
namespace Serveur\Lib\FichierChargement;

abstract class AbstractChargeurFichier
{
    /**
     * @param string $locationFichier
     * @return mixed
     */
    abstract public function chargerFichier($locationFichier);
}