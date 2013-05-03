<?php
namespace AlaroxRestServeur\Serveur\Lib;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\Utils\Constante;

class TypeDetector
{
    /**
     * @var array
     */
    private $_constanteMimes;

    /**
     * @param array $mimesTypes
     * @throws ArgumentTypeException
     */
    public function __construct($mimesTypes)
    {
        if (!is_array($mimesTypes)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $mimesTypes);
        }

        $this->_constanteMimes = $mimesTypes;
    }

    /**
     * @param string $clefMimeExtension
     * @return string
     */
    public function getMimeType($clefMimeExtension)
    {
        if (!isNull($this->_constanteMimes[$clefMimeExtension])) {
            return $this->_constanteMimes[$clefMimeExtension];
        } else {
            return '*/*';
        }
    }

    /**
     * @param string $enteteHttpAccept
     * @return array
     */
    public function extraireMimesTypeHeader($enteteHttpAccept)
    {
        $allType = explode(',', $enteteHttpAccept);
        $tabTypesTrouves = array();

        foreach ($allType as $unType) {
            if (strpos($unType = strtolower($unType), ';') !== false) {
                $unType = substr($unType, 0, strpos($unType, ';'));
            }

            foreach ($this->_constanteMimes as $uneExtension => $unFormatMime) {
                if (strcmp($unFormatMime, $unType) == 0) {
                    $tabTypesTrouves[] = $uneExtension;
                }
            }
        }

        return $tabTypesTrouves;
    }
}