<?php
	namespace Tests;

	class TestCase extends \PHPUnit_Framework_TestCase {

		static $boolArray = array(false => 'false', true => 'true');

		protected function createMock($type) {
			/** @var $mock \PHPUnit_Framework_MockObject_MockObject */
			$mock = null;
			$static = false;

			switch($type) {
				case 'Constante':
					$static = true;
					$mock = $this->getMockConstante();
					break;
				case 'Server':
					$mock = $this->getMockServer();
					break;
				case 'RestRequete':
					$mock = $this->getMockRestRequete();
					break;
				case 'RestReponse':
					$mock = $this->getMockRestReponse();
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

				if (!isNull($uneMethode[2])) {
					if(is_bool($uneMethode[2])) {
						$var = self::$boolArray[$uneMethode[2]];
					} elseif(is_array($uneMethode[2])) {
						$var = $this->arrayToStringPhp($uneMethode[2]);
					} else {
						$var = '"'.$uneMethode[2].'"';
					}

					$will = "->will(\$this->returnValue(".$var."));";
				} else {
					$will = ";";
				}

				if(!isNull($uneMethode[1])) {
					if(is_array($uneMethode[1])) {
						eval("$enteteMock(\$this->exactly(".count($uneMethode[1])."))$methode;");
						foreach($uneMethode[1] as $unParametre) {
							$with = "->with(\"".$unParametre."\")";

							eval("$enteteMock(\$this->at($cptAt))$methode$with$will");

							$cptAt++;
						}
					} else {
						if (!isNull($uneMethode[1])) {
							$with = "->with(\"".$uneMethode[1]."\")";
						} else {
							$with = "";
						}

						eval("$enteteMock(\$this->once())$methode$with$will");
					}
				} else {
					eval("$enteteMock(\$this->once())$methode$will");
				}
				$cptAt++;
			}

			return $mock;
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

		protected function getMockConstante() {
			return $this->getMock('Serveur\Utils\Constante');
		}

		protected function getMockServer() {
			return $this->getMock('Serveur\Rest\Server');
		}

		protected function getMockRestRequete() {
			return $this->getMock('Serveur\Rest\RestRequete');
		}

		protected function getMockRestReponse() {
			return $this->getMock('Serveur\Rest\RestReponse');
		}
	}