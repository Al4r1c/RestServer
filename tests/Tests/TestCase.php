<?php
	namespace Tests;

	class TestCase extends \PHPUnit_Framework_TestCase {

		static $boolArray = array(false => 'false', true => 'true');

		protected function createMock($type) {
			/** @var $mock \PHPUnit_Framework_MockObject_MockObject */
			$mock = null;
			$static = false;

			switch(strtolower($type)) {
				case 'config':
					$mock = $this->getMockConfig();
					break;
				case 'constante':
					$static = true;
					$mock = $this->getMockConstante();
					break;
				case 'restrequete':
					$mock = $this->getMockRestRequete();
					break;
				case 'restreponse':
					$mock = $this->getMockRestReponse();
				break;
				case 'server':
					$mock = $this->getMockServer();
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

			$cptAt = 0;
			foreach(array_slice(func_get_args(), 1) as $uneMethode) {
				$methode = "->method(\"".$uneMethode[0]."\")";

				if(!isNull($uneMethode[1])) {
					if(is_array($uneMethode[1])) {
						//eval("$enteteMock(\$this->exactly(".count($uneMethode[1])."))$methode;");
						$i = 0;

						foreach($uneMethode[1] as $unParametre) {
							$with = "->with(\"".$unParametre."\")";
							$will = $this->makeWill($uneMethode[2][$i]);

							eval("$enteteMock(\$this->at($cptAt))$methode$with$will");

							$cptAt++;
							$i++;
						}
					} else {
						if (!isNull($uneMethode[1])) {
							$with = "->with(\"".$uneMethode[1]."\")";
						} else {
							$with = "";
						}

						$will = $this->makeWill($uneMethode[2]);

						eval("$enteteMock(\$this->once())$methode$with$will");
					}
				} else {
					$will = $this->makeWill($uneMethode[2]);
					eval("$enteteMock(\$this->once())$methode$will");
				}
				$cptAt++;
			}

			return $mock;
		}

		private function makeWill($element) {
			if (!isNull($element)) {
				if(is_bool($element)) {
					$var = self::$boolArray[$element];
				} elseif(is_array($element)) {
					$var = $this->arrayToStringPhp($element);
				} else {
					$var = '"'.$element.'"';
				}

				$will = "->will(\$this->returnValue(".$var."));";
			} else {
				$will = ";";
			}

			return $will;
		}

		private function arrayToStringPhp(array $array) {
			$string = 'array(';
			foreach($array as $clef => $valeur) {
				if(is_array($valeur)) {
					$valeur = $this->arrayToStringPhp($valeur);
				} else {
					$valeur = '"'.$valeur.'"';
				}
				$string .= '"'.$clef.'" => '.$valeur.',';
			}
			return substr($string, 0, -1).')';
		}

		protected function getMockConfig() {
			return $this->getMock('Serveur\Config\Config');
		}

		protected function getMockConstante() {
			return $this->getMock('Serveur\Utils\Constante');
		}

		protected function getMockRestRequete() {
			return $this->getMock('Serveur\Rest\RestRequete');
		}

		protected function getMockRestReponse() {
			return $this->getMock('Serveur\Rest\RestReponse');
		}

		protected function getMockServer() {
			return $this->getMock('Serveur\Rest\Server');
		}
	}