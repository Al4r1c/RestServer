<?php
namespace Logging\I18n;

use Serveur\Lib\XMLParser\XMLParser;

class TradManager
{
    /**
     * @var XMLParser
     */
    private $_fichierTraductionDefaut;

    /**
     * @param \Serveur\Lib\XMLParser\XMLParser $fichierTradDef
     * @throws \Exception
     */
    public function setFichierTraduction(XMLParser $fichierTradDef)
    {
        if (!$fichierTradDef->isValide()) {
            throw new \Exception('Traduction object is invalid.');
        }

        $this->_fichierTraductionDefaut = $fichierTradDef;
    }

    /**
     * @param string $section
     * @param string $identifier
     * @return string
     */
    private function getTraduction($section, $identifier)
    {
        $xmlElementsCorrespondants =
            $this->_fichierTraductionDefaut->getConfigValeur($section . '.message[code=' . $identifier . ']');

        if (!empty($xmlElementsCorrespondants)) {
            return $xmlElementsCorrespondants[0]->getValeur();
        } else {
            return '{' . $section . '.' . $identifier . '}';
        }
    }

    /**
     * @param string $contenu
     * @return string
     * @throws \Exception
     */
    public function recupererChaineTraduite($contenu)
    {
        if (isNull($this->_fichierTraductionDefaut)) {
            throw new \Exception('No traduction object set.');
        }

        if (preg_match_all('/{.*?}/', $contenu, $stringTrouve)) {
            foreach (array_unique($stringTrouve[0]) as $valeur) {
                $contenu = str_replace(
                    $valeur, $this->getTraduction(
                        substr($valeur, 1, strpos($valeur, '.') - 1), substr($valeur, strpos($valeur, '.') + 1, -1)
                    ), $contenu
                );
            }
        }

        return $contenu;
    }
}