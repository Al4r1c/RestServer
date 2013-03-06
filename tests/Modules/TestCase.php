<?php
    namespace Modules;

    class TestCase extends \PHPUnit_Framework_TestCase {

        private static $boolArray = array(false => 'false', true => 'true');
        private $tabTricks = array();

        protected function genererEvals($tabMethodes) {
            $tabEvals = array();

            foreach ($tabMethodes as $uneMethode) {
                $tabMethodes[] = $uneMethode[0];
                $methode = "->method(\"" . $uneMethode[0] . "\")";

                if (!isNull($uneMethode[1])) {
                    $with = "->with(" . $this->getPlainVar($uneMethode[1]) . ")";
                } else {
                    $with = "";
                }

                $will = $this->makeWill($uneMethode[2]);

                $tabEvals[$uneMethode[0]][] = array('methode' => $methode, 'with' => $with, 'will' => $will);
            }

            return $tabEvals;
        }

        protected function informerMock($mock, $enteteMock, $tabEvals) {
            $cptAt = 0;
            foreach ($tabEvals as $methodeEval) {
                if (count($methodeEval) == 1) {
                    $methode = $methodeEval[0]['methode'];
                    $with = $methodeEval[0]['with'];
                    $will = $methodeEval[0]['will'];

                    eval("$enteteMock(\$this->atLeastOnce())$methode$with$will");

                    $cptAt++;
                } else {
                    foreach ($methodeEval as $evalAEffectuer) {
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

        protected function createStaticMock($type) {
            $tabEvals = $this->genererEvals(array_slice(func_get_args(), 1));

            $factoryMock = new FactoryMock();
            $mock = $factoryMock->createMock($type, array_unique(array_keys($tabEvals)));

            return $this->informerMock($mock, "\$mock::staticExpects", $tabEvals);
        }

        protected function createMock($type) {
            $tabEvals = $this->genererEvals(array_slice(func_get_args(), 1));

            $factoryMock = new FactoryMock();
            $mock = $factoryMock->createMock($type, array_unique(array_keys($tabEvals)));

            return $this->informerMock($mock, "\$mock->expects", $tabEvals);
        }

        private function getPlainVar($element) {
            if (is_bool($element)) {
                $var = self::$boolArray[$element];
            } elseif (is_array($element)) {
                $var = $this->arrayToStringPhp($element);
            } elseif (is_object($element)) {
                $this->tabTricks[] = $element;
                end($this->tabTricks);
                $var = "\$this->tabTricks[" . key($this->tabTricks) . "]";
            } elseif (is_numeric($element)) {
                $var = $element;
            } else {
                $var = '"' . addslashes($element) . '"';
            }

            return $var;
        }

        private function makeWill($element) {
            $will = ";";

            if (!is_callable($element)) {
                $returnType = 'returnValue';
            } else {
                $returnType = 'returnCallback';
            }

            if (!isNull($element)) {
                $will = "->will(\$this->".$returnType."(" . $this->getPlainVar($element) . "))" . $will;
            }

            return $will;
        }

        private function arrayToStringPhp(array $array) {
            $string = 'array(';
            foreach ($array as $clef => $valeur) {
                $string .= '"' . $clef . '" => ' . $this->getPlainVar($valeur) . ',';
            }

            return substr($string, 0, -1) . ')';
        }
    }