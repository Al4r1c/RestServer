<?php
	namespace Serveur\Renderers;

	class Xml extends \Serveur\Renderers\AbstractRenderer {
		/**
		 * @param array $donnees
		 * @return string
		 */
		protected function genererRendu(array $donnees) {
			$simpleXmlObject = new \SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");

			$this->arrayToXml($donnees, $simpleXmlObject);

			return $simpleXmlObject->asXML();
		}

		/**
		 * @param array $contenu
		 * @param \SimpleXMLElement $simpleXmlObject
		 */
		private function arrayToXml($contenu, \SimpleXMLElement &$simpleXmlObject) {
			foreach($contenu as $clef => $value) {
				if(is_array($value)) {
					if(!is_numeric($clef)) {
						$subnode = $simpleXmlObject->addChild("$clef");
						$this->arrayToXml($value, $subnode);
					} else {
						$this->arrayToXml($value, $simpleXmlObject);
					}
				} else {
					$simpleXmlObject->addChild("$clef", "$value");
				}
			}
		}
	}