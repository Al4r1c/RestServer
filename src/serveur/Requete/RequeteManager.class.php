<?php
namespace Serveur\Requete;

use Logging\Displayer\AbstractDisplayer;
use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Lib\TypeDetector;
use Serveur\Requete\Server\Server;
use Serveur\Utils\Constante;
use Serveur\Utils\Tools;

class RequeteManager
{
    /**
     * @var Server
     */
    private $_server;

    /**
     * @param Server $server
     * @throws ArgumentTypeException
     */
    public function setServer($server)
    {
        if (!$server instanceof Server) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Requete\Server\Server', $server);
        }

        $this->_server = $server;
    }

    /**
     * @throws MainException
     * @return string
     */
    public function getMethode()
    {
        $method = $this->_server->getUneVariableServeur('REQUEST_METHOD');

        if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new MainException(20000, 400, $method);
        }

        return $method;
    }

    /**
     * @throws ArgumentTypeException
     * @throws MainException
     * @return array
     */
    public function getHttpAccept()
    {
        $httpAccept = $this->_server->getUneVariableServeur('HTTP_ACCEPT');

        if (!is_string($httpAccept)) {
            throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $httpAccept);
        }

        return $httpAccept;
    }

    /**
     * @throws ArgumentTypeException
     * @throws MainException
     * @return array
     */
    public function getFormatsDemandes()
    {
        $typeDetector = new TypeDetector(Constante::chargerConfig('mimes'));
        $formatsTrouves = $typeDetector->extraireMimesTypeHeader($this->getHttpAccept());

        if (isNull($formatsTrouves)) {
            throw new MainException(20001, 400);
        }

        return $formatsTrouves;
    }

    /**
     * @throws ArgumentTypeException
     * @return array
     */
    public function getUriVariables()
    {
        $uri = $this->_server->getUneVariableServeur('REQUEST_URI');

        if (!is_string($uri)) {
            throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $uri);
        }

        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        return
            array_map('rawurlencode', explode('/', trim(preg_replace('%([^:])([/]{2,})%', '\\1/', $uri), '/')));
    }

    /**
     * @param string $clef
     * @return string|null
     */
    public function getUriVariable($clef)
    {
        if (array_key_exists($clef, $this->getUriVariables())) {
            return $this->getUriVariables()[$clef];
        } else {
            return null;
        }
    }

    /**
     * @throws ArgumentTypeException
     * @throws MainException
     * @return string|null
     */
    public function getPlainParametres()
    {
        return $this->getParametresForMethode($this->getMethode());
    }

    /**
     * @throws ArgumentTypeException
     * @throws MainException
     * @return array
     */
    public function getParametres()
    {
        $donnees = array();

        switch ($methode = strtoupper($this->getMethode())) {
            case 'GET':
                parse_str($this->getParametresForMethode($methode), $donnees);
                break;
            case 'POST':
            case 'PUT':
                if (is_null($contentType = $this->getContentType()) || strcmp($contentType, 'text/plain') == 0) {
                    parse_str($this->getParametresForMethode($methode), $donnees);
                } elseif (strcmp($contentType, 'application/json') == 0) {
                    $donnees = json_decode($this->getParametresForMethode($methode), true);
                } elseif (strcmp($contentType, 'application/xml') == 0) {
                    $xmlParsee = new \XMLParser();
                    $xmlParsee->setAndParseContent($this->getParametresForMethode($methode));

                    $donnees = $this->dataToAssocArrayCompatible($xmlParsee->getParsedData()->getChildren());
                } else {
                    throw new MainException(20004, 400, $contentType);
                }
                break;
        }

        return array_filter($donnees, 'strlen');
    }

    /**
     * @param string $methode
     * @return null|string
     */
    private function getParametresForMethode($methode)
    {
        switch (strtoupper($methode)) {
            case 'GET':
                return $this->_server->getUneVariableServeur('QUERY_STRING');
                break;
            case 'POST':
            case 'PUT':
                return $this->_server->getUneVariableServeur('PHP_INPUT');
                break;
        }

        return null;
    }

    /**
     * @return string
     * @throws MainException
     */
    public function getAuthorization()
    {
        $auth = $this->_server->getUneVariableServeur('REDIRECT_HTTP_AUTHORIZATION');

        if (!empty($auth) && !startsWith($auth, 'ARS ')) {
            throw new MainException(20005, 400, $auth);
        }

        return $auth;
    }

    /**
     * @param \XMLElement[] $tabXmlElements
     * @return array
     */
    private function dataToAssocArrayCompatible($tabXmlElements)
    {
        $result = array();

        foreach ($tabXmlElements as $unElement) {
            if ($unElement->hasChildren()) {
                $result[$unElement->getUnAttribut('attr')] =
                    $this->dataToAssocArrayCompatible($unElement->getChildren());
            } else {
                $result[$unElement->getUnAttribut('attr')] = $unElement->getValue();
            }
        }

        return $result;
    }

    /**
     * @throws ArgumentTypeException
     * @return \DateTime
     */
    public function getDateRequete()
    {
        return new \DateTime($this->_server->getUneVariableServeur('HTTP_DATE'));
    }

    /**
     * @throws ArgumentTypeException
     * @throws MainException
     * @return string
     */
    public function getIp()
    {
        $ip = $this->_server->getUneVariableServeur('REMOTE_ADDR');

        if (!is_string($ip)) {
            throw new ArgumentTypeException(1000, 400, __METHOD__, 'string', $ip);
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new MainException(20002, 400, $ip);
        }

        return $ip;
    }

    public function getContentType()
    {
        $contentType = $this->_server->getUneVariableServeur('CONTENT_TYPE');

        if (startsWith('*/*', $contentType) == 0) {
            $contentType = 'text/plain';
        }

        if (!is_null($contentType) && !Tools::isValideFormat($contentType)) {
            throw new MainException(20003, 400, $contentType);
        }

        return $contentType;
    }

    /**
     * @param AbstractDisplayer[] $_observeurs
     */
    public function logRequete($_observeurs)
    {
        foreach ($_observeurs as $unObserveur) {
            $unObserveur->ecrireLogRequete($this);
        }
    }
}