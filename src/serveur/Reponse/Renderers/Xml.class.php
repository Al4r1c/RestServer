<?php
namespace Serveur\Reponse\Renderers;

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
        foreach ($contenu as $clef => $value) {
            if (is_array($value)) {
                $subnode = $simpleXmlObject->addChild("element");
                $subnode->addAttribute("attr", $clef);
                $this->arrayToXml($value, $subnode);
            } else {
                $simpleXmlObject->addChild("element", "$value")->addAttribute("attr", $clef);
            }
        }
    }
}