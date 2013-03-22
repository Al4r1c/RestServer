<?php
namespace Logging\I18n;

use Serveur\Lib\Fichier;
use Serveur\Utils\FileManager;

class I18nManager
{
    /**
     * @var string
     */
    private $_langueDefaut;
    /**
     * @var string[]
     */
    private $_languesDisponibles;

    /**
     * @param string $langueDefaut
     * @param array $languesDispo
     */
    public function setConfig($langueDefaut, array $languesDispo)
    {
        $this->setLangueDefaut($langueDefaut);
        $this->setLangueDispo($languesDispo);
    }

    /**
     * @param string $langueDefaut
     * @throws \Exception
     */
    public function setLangueDefaut($langueDefaut)
    {
        if (isNull($langueDefaut)) {
            throw new \Exception('Default language is not set properly.');
        }

        $this->_langueDefaut = $langueDefaut;
    }

    /**
     * @param array $languesDispo
     * @throws \Exception
     */
    public function setLangueDispo(array $languesDispo)
    {
        if (isNull($languesDispo)) {
            throw new \Exception('No available language set.');
        }

        $this->_languesDisponibles = $languesDispo;
    }

    /**
     * @return \XMLParser
     * @throws \Exception
     * */
    public function getFichierTraduction()
    {
        if (array_key_exists(strtoupper($this->_langueDefaut), $this->_languesDisponibles)) {
            $nomFichierLangueDefaut = $this->_languesDisponibles[strtoupper($this->_langueDefaut)];
        } else {
            $nomFichierLangueDefaut = reset($this->_languesDisponibles);
        }

        $fichierTraductionParDefaut = $this->getFichier($nomFichierLangueDefaut);

        if ($fichierTraductionParDefaut->fichierExiste() &&
            $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut)->isValidXML()
        ) {
            return $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut);
        } else {
            if (($langueChoisiAleatoirement = $this->getUneTraductionAleatoire()) !== false) {
                return $this->recupererXmlParserDepuisFichier(reset($langueChoisiAleatoirement));
            } else {
                throw new \Exception('No valid translation file set or found.');
            }
        }
    }

    /**
     * @param $nomFichier
     * @return Fichier
     */
    protected function getFichier($nomFichier)
    {
        $fichier = FileManager::getFichier();
        $fichier->setFichierParametres($nomFichier . '.xml', '/public/i18n');

        return $fichier;
    }

    /**
     * @param Fichier $fichier
     * @return \XMLParser
     */
    private function recupererXmlParserDepuisFichier(Fichier $fichier)
    {
        return $fichier->chargerFichier();
    }

    /**
     * @return array|bool
     */
    private function getUneTraductionAleatoire()
    {
        foreach ($this->_languesDisponibles as $uneLangueDispo => $classeLangue) {
            $traductionDisponible = $this->getFichier($classeLangue);

            if ($traductionDisponible->fichierExiste() &&
                $this->recupererXmlParserDepuisFichier($traductionDisponible)->isValidXML()
            ) {
                return array($uneLangueDispo => $traductionDisponible);
            }
        }

        return false;
    }
}