<?php
	namespace Tests;

	class TestCase extends \PHPUnit_Framework_TestCase {

		static $boolArray = array(false => 'false', true => 'true');

		protected function createMock($type) {
			/** @var $mock \PHPUnit_Framework_MockObject_MockObject */
			$mock = null;
			$static = false;
			$tabMethode = array();
			$tabEvals = array();

			foreach(array_slice(func_get_args(), 1) as $uneMethode) {
				$tabMethode[] = $uneMethode[0];
				$methode = "->method(\"".$uneMethode[0]."\")";

				if (!isNull($uneMethode[1])) {
					$with = "->with(".$this->getPlainVar($uneMethode[1], '$uneMethode[1]').")";
				} else {
					$with = "";
				}

				$will = $this->makeWill($uneMethode[2], '$uneMethode[2]');

				$tabEvals[] = $methode.$with.$will;
			}

			$tabMethode = array_unique($tabMethode);
			switch(strtolower($type)) {
				case 'abstractrenderer':
					$mock = $this->getMockAbstractRenderer($tabMethode);
					break;
				case 'abstractchargeurfichier':
					$mock = $this->getMockAbstractChargeur($tabMethode);
					break;
				case 'config':
					$mock = $this->getMockConfig($tabMethode);
					break;
				case 'constante':
					$static = true;
					$mock = $this->getMockConstante($tabMethode);
					break;
				case 'fichier':
					$mock = $this->getMockFichier($tabMethode);
					break;
				case 'headermanager':
					$mock = $this->getMockHeadersManager($tabMethode);
					break;
				case 'restrequete':
					$mock = $this->getMockRestRequete($tabMethode);
					break;
				case 'restreponse':
					$mock = $this->getMockRestReponse($tabMethode);
					break;
				case 'server':
					$mock = $this->getMockServer($tabMethode);
					break;
				default:
					new \Exception('Mock type not found.');
					break;
			}

			if (!$static) {
				$enteteMock = "\$mock->expects";
			} else {
				$enteteMock = "\$mock::staticExpects";
			}

			foreach($tabEvals as $clef => $unEvalAEffectuer) {
				eval("$enteteMock(\$this->at($clef))$unEvalAEffectuer");
			}

			return $mock;
		}

		private function getPlainVar(&$element, $plainName) {
			if(is_bool($element)) {
				$var = self::$boolArray[$element];
			} elseif(is_array($element)) {
				$var = $this->arrayToStringPhp($element, $plainName);
			} elseif(is_object($element)) {
				$var = $plainName;
			} else {
				$var = '"'.addslashes($element).'"';
			}

			return $var;
		}

		private function makeWill($element, $plainName) {
			$will = ";";

			if (!isNull($element)) {
				$will = "->will(\$this->returnValue(".$this->getPlainVar($element, $plainName)."))".$will;
			}

			return $will;
		}

		private function arrayToStringPhp(array $array, $plainName) {
			$string = 'array(';
			foreach($array as $clef => $valeur) {
				$string .= '"'.$clef.'" => '.$this->getPlainVar($valeur, $plainName.'['.$clef.']').',';
			}
			return substr($string, 0, -1).')';
		}

		protected function getMockAbstractRenderer($methodes = array()) {
			return $this->getMockForAbstractClass('Serveur\Renderers\AbstractRenderer');
		}

		private function getMockAbstractChargeur($tabMethode) {
			return $this->getMockForAbstractClass('Serveur\Lib\FichierChargement\AbstractChargeurFichier');
		}

		protected function getMockConfig($methodes = array()) {
			return $this->getMock('Serveur\Config\Config', $methodes);
		}

		protected function getMockConstante($methodes = array()) {
			return $this->getMock('Serveur\Utils\Constante', $methodes);
		}

		protected function getMockFichier($methodes = array()) {
			return $this->getMock('Serveur\Lib\Fichier', $methodes);
		}

		protected function getMockHeadersManager($methodes = array()) {
			return $this->getMock('Serveur\Rest\HeaderManager', $methodes);
		}

		protected function getMockRestRequete($methodes = array()) {
			return $this->getMock('Serveur\Rest\RestRequete', $methodes);
		}

		protected function getMockRestReponse($methodes = array()) {
			return $this->getMock('Serveur\Rest\RestReponse', $methodes);
		}

		protected function getMockServer($methodes = array()) {
			return $this->getMock('Serveur\Rest\Server', $methodes);
		}
	}