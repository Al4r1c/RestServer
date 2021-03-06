<?php
namespace AlaroxRestServeur\Serveur\Reponse\Renderers;

class Xml extends AbstractRenderer
{
    /**
     * @param array $donnees
     * @return string
     */
    protected function genererRendu(array $donnees)
    {
        $simpleXmlObject = new \SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");

        $this->arrayToXml($donnees, $simpleXmlObject);

        return $simpleXmlObject->asXML();
    }

    /**
     * @param array $contenu
     * @param \SimpleXMLElement $simpleXmlObject
     */
    private function arrayToXml($contenu, \SimpleXMLElement &$simpleXmlObject)
    {
        foreach ($contenu as $clef => $valeur) {
            if (is_array($valeur)) {
                if (is_numeric(key($valeur))) {
                    foreach ($valeur as $uneValeur) {
                        if (!is_array($uneValeur)) {
                            $simpleXmlObject->addChild($clef, $uneValeur);
                        } else {
                            $subnode = $simpleXmlObject->addChild($clef);
                            $this->arrayToXml($uneValeur, $subnode);
                        }
                    }
                } else {
                    $subnode = $simpleXmlObject->addChild($clef);
                    $this->arrayToXml($valeur, $subnode);
                }
            } elseif (is_object($valeur) && get_class($valeur) == 'DateTime') {
                $simpleXmlObject->addChild($clef, $valeur->format('Y-m-d H:i:s e'));
            } else {
                $simpleXmlObject->addChild($clef, $valeur);
            }
        }
    }
}