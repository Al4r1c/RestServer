<?php
namespace AlaroxRestServeur\Serveur\Reponse\Header;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException;
use AlaroxRestServeur\Serveur\Utils\Tools;

class Header
{
    /**
     * @var array
     */
    private $_headers = array();

    /**
     * @param string $champ
     * @param string $valeur
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function ajouterHeader($champ, $valeur)
    {
        if (!is_string($champ)) {
            throw new ArgumentTypeException(500, 'string', $champ);
        }

        if (!is_string($valeur)) {
            throw new ArgumentTypeException(500, 'string', $valeur);
        }

        if (!Tools::isValideResponseHeader($champ)) {
            throw new MainException(40100, 500, $champ);
        }

        $this->_headers[$champ] = $valeur;
    }

    public function envoyerHeaders()
    {
        foreach ($this->_headers as $champHeader => $valeurHeader) {
            header($champHeader . ': ' . $valeurHeader, true);
        }
    }
}