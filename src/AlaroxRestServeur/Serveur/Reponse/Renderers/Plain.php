<?php
namespace AlaroxRestServeur\Serveur\Reponse\Renderers;

class Plain extends AbstractRenderer
{
    /**
     * @param array $donnees
     * @return string
     */
    protected function genererRendu(array $donnees)
    {
        return arrayToString($donnees);
    }
}