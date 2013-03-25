<?php
namespace Logging\I18n;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;

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

        if ($fichierTraductionParDefaut->fileExist() &&
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
     * @return File
     */
    protected function getFichier($nomFichier)
    {
        $alaroxFileManager = new AlaroxFile();

        return $alaroxFileManager->getFile(BASE_PATH . '/public/i18n/' . $nomFichier . '.xml');
    }

    /**
     * @param File $fichier
     * @return \XMLParser
     */
    private function recupererXmlParserDepuisFichier(File $fichier)
    {
        return $fichier->loadFile();
    }

    /**
     * @return array|bool
     */
    private function getUneTraductionAleatoire()
    {
        foreach ($this->_languesDisponibles as $uneLangueDispo => $classeLangue) {
            $traductionDisponible = $this->getFichier($classeLangue);

            if ($traductionDisponible->fileExist() &&
                $this->recupererXmlParserDepuisFichier($traductionDisponible)->isValidXML()
            ) {
                return array($uneLangueDispo => $traductionDisponible);
            }
        }

        return false;
    }
}