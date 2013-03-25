<?php
namespace Serveur\Lib;

use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Utils\Constante;
use Serveur\Utils\Tools;

class ObjetReponse
{
    /**
     * @var int
     */
    private $_statusHttp;

    /**
     * @var array
     */
    private $_donneesReponse;

    /**
     * @var string
     */
    private $_format;

    public function __construct($statusHttp = 200, $donneesReponse = array())
    {
        $this->setStatusHttp($statusHttp);
        $this->setDonneesReponse($donneesReponse);
    }

    /**
     * @return int
     */
    public function getStatusHttp()
    {
        return $this->_statusHttp;
    }

    /**
     * @return array
     */
    public function getDonneesReponse()
    {
        return $this->_donneesReponse;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * @param int $statusHttp
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function setStatusHttp($statusHttp)
    {
        if (!is_int($statusHttp)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'int', $statusHttp);
        }

        if (!Tools::isValideHttpCode($statusHttp)) {
            throw new MainException(10100, 500, $statusHttp);
        }

        $this->_statusHttp = $statusHttp;
    }

    /**
     * @param array $donneesReponse
     * @throws ArgumentTypeException
     */
    public function setDonneesReponse($donneesReponse)
    {
        if (!is_array($donneesReponse)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $donneesReponse);
        }

        $this->_donneesReponse = $donneesReponse;
    }

    /**
     * @param int $statusHttp
     */
    public function setErreurHttp($statusHttp)
    {
        $infoHttpCode = Constante::chargerConfig('httpcode')[$statusHttp];

        $this->setStatusHttp($statusHttp);
        $this->setDonneesReponse(
            array('Code' => $statusHttp, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1])
        );
    }

    /**
     * @param string $format
     * @throws MainException
     */
    public function setFormat($format)
    {
        if (!Tools::isValideFormat($format)) {
            throw new MainException(10101, 500, $format);
        }

        $this->_format = $format;
    }
}