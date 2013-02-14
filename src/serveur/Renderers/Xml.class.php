<?php
	namespace Serveur\Renderers;

	class Xml extends \Serveur\Renderers\AbstractRenderer {
		public function render(array $donnees) {
			$simpleXmlObject = new \SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");

			$this->array_to_xml($donnees, $simpleXmlObject);

			return $simpleXmlObject->asXML();
		}

		private function array_to_xml($content, \SimpleXMLElement &$simpleXmlObject) {
			foreach($content as $key => $value) {
				if(is_array($value)) {
					if(!is_numeric($key)){
						$subnode = $simpleXmlObject->addChild("$key");
						$this->array_to_xml($value, $subnode);
					} else{
						$this->array_to_xml($value, $simpleXmlObject);
					}
				} else {
					$simpleXmlObject->addChild("$key","$value");
				}
			}
		}
	}