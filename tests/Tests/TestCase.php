<?php
	namespace Tests;

	class TestCase extends \PHPUnit_Framework_TestCase {

		private static $boolArray = array(false => 'false', true => 'true');
		private $tabTricks = array();

		protected function createMock($type) {
			/** @var $mock \PHPUnit_Framework_MockObject_MockObject */
			$mock = null;
			$static = false;
			$tabEvals = array();

			foreach(array_slice(func_get_args(), 1) as $uneMethode) {
				$tabMethode[] = $uneMethode[0];
				$methode = "->method(\"" . $uneMethode[0] . "\")";

				if(!isNull($uneMethode[1])) {
					$with = "->with(" . $this->getPlainVar($uneMethode[1]) . ")";
				} else {
					$with = "";
				}

				$will = $this->makeWill($uneMethode[2]);

				$tabEvals[$uneMethode[0]][] = array('methode' => $methode, 'with' => $with, 'will' => $will);
			}

			switch(strtolower($type)) {
				case 'abstractrenderer':
					$mock = $this->getMockAbstractRenderer();
					break;
				case 'abstractchargeurfichier':
					$mock = $this->getMockAbstractChargeur();
					break;
				case 'config':
					$mock = $this->getMockConfig(array_unique(array_keys($tabEvals)));
					break;
				case 'constante':
					$static = true;
					$mock = $this->getMockConstante(array_unique(array_keys($tabEvals)));
					break;
				case 'fichier':
					$mock = $this->getMockFichier(array_unique(array_keys($tabEvals)));
					break;
				case 'filesystem':
					$mock = $this->getMockFileSystem(array_unique(array_keys($tabEvals)));
					break;
				case 'headermanager':
					$mock = $this->getMockHeadersManager(array_unique(array_keys($tabEvals)));
					break;
				case 'i18nmanager':
					$mock = $this->getMockI18nManager(array_unique(array_keys($tabEvals)));
					break;
				case 'restrequete':
					$mock = $this->getMockRestRequete(array_unique(array_keys($tabEvals)));
					break;
				case 'restreponse':
					$mock = $this->getMockRestReponse(array_unique(array_keys($tabEvals)));
					break;
				case 'server':
					$mock = $this->getMockServer(array_unique(array_keys($tabEvals)));
					break;
				case 'xmlelement':
					$mock = $this->getMockXmlElement(array_unique(array_keys($tabEvals)));
					break;
				case 'xmlparser':
					$mock = $this->getMockXmlParser(array_unique(array_keys($tabEvals)));
					break;
				default:
					new \Exception('Mock type not found.');
					break;
			}

			if(!$static) {
				$enteteMock = "\$mock->expects";
			} else {
				$enteteMock = "\$mock::staticExpects";
			}

			$cptAt = 0;
			foreach($tabEvals as $methodeEval) {
				if(count($methodeEval) == 1) {
					$methode = $methodeEval[0]['methode'];
					$with = $methodeEval[0]['with'];
					$will = $methodeEval[0]['will'];

					eval("$enteteMock(\$this->atLeastOnce())$methode$with$will");

					$cptAt++;
				} else {
					foreach($methodeEval as $evalAEffectuer) {
						$methode = $evalAEffectuer['methode'];
						$with = $evalAEffectuer['with'];
						$will = $evalAEffectuer['will'];
						eval("$enteteMock(\$this->at($cptAt))$methode$with$will");

						$cptAt++;
					}
				}
			}

			$this->tabTricks = array();

			return $mock;
		}

		private function getPlainVar($element) {
			if(is_bool($element)) {
				$var = self::$boolArray[$element];
			} elseif(is_array($element)) {
				$var = $this->arrayToStringPhp($element);
			} elseif(is_object($element)) {
				$this->tabTricks[] = $element;
				end($this->tabTricks);
				$var = "\$this->tabTricks[" . key($this->tabTricks) . "]";
			} else {
				$var = '"' . addslashes($element) . '"';
			}

			return $var;
		}

		private function makeWill($element) {
			$will = ";";

			if(!isNull($element)) {
				$will = "->will(\$this->returnValue(" . $this->getPlainVar($element) . "))" . $will;
			}

			return $will;
		}

		private function arrayToStringPhp(array $array) {
			$string = 'array(';
			foreach($array as $clef => $valeur) {
				$string .= '"' . $clef . '" => ' . $this->getPlainVar($valeur) . ',';
			}

			return substr($string, 0, -1) . ')';
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Renderers\AbstractRenderer */
		protected function getMockAbstractRenderer() {
			return $this->getMockForAbstractClass('Serveur\Renderers\AbstractRenderer');
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FichierChargement\AbstractChargeurFichier */
		private function getMockAbstractChargeur() {
			return $this->getMockForAbstractClass('Serveur\Lib\FichierChargement\AbstractChargeurFichier');
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Config\Config */
		protected function getMockConfig($methodes = array()) {
			return $this->getMock('Serveur\Config\Config', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Utils\Constante */
		protected function getMockConstante($methodes = array()) {
			return $this->getMock('Serveur\Utils\Constante', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\Fichier */
		protected function getMockFichier($methodes = array()) {
			return $this->getMock('Serveur\Lib\Fichier', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FileSystem */
		protected function getMockFileSystem($methodes = array()) {
			return $this->getMock('Serveur\Lib\FileSystem', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\HeaderManager */
		protected function getMockHeadersManager($methodes = array()) {
			return $this->getMock('Serveur\Rest\HeaderManager', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\I18n\I18nManager */
		protected function getMockI18nManager($methodes = array()) {
			return $this->getMock('Serveur\Rest\HeaderManager', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestRequete */
		protected function getMockRestRequete($methodes = array()) {
			return $this->getMock('Serveur\Rest\RestRequete', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestReponse */
		protected function getMockRestReponse($methodes = array()) {
			return $this->getMock('Serveur\Rest\RestReponse', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\Server */
		protected function getMockServer($methodes = array()) {
			return $this->getMock('Serveur\Rest\Server', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLElement */
		protected function getMockXmlElement($methodes = array()) {
			return $this->getMock('Serveur\Lib\XMLParser\XMLElement', $methodes);
		}

		/** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLParser */
		protected function getMockXmlParser($methodes = array()) {
			return $this->getMock('Serveur\Lib\XMLParser\XMLParser', $methodes);
		}
	}