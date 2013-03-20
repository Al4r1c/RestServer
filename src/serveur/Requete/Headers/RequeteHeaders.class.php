<?php
namespace Serveur\Requete\Headers;

use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Utils\Tools;

class RequeteHeaders
{
    /**
     * @var array
     */
    private $_headers;

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @param array $headers
     * @throws MainException
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $header => $valeurHeader) {
            if (!Tools::isValideRequestHeader($header)) {
                throw new MainException(20200, 400, $header, $valeurHeader);
            }
        }

        $this->_headers = $headers;
    }
}