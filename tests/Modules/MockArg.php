<?php
    namespace Modules;

    class MockArg
    {
        /**
         * @var string
         */
        private $_methode;

        /**
         * @var array
         */
        private $_arguments;

        /**
         * @var mixed
         */
        private $_returnValeur;

        /**
         * @param string $methode
         * @param mixed $return
         * @param array $arg
         */
        public function __construct($methode, $return = null, $arg = array())
        {
            $this->setMethode($methode);
            $this->setArguments($arg);
            $this->setReturnValeur($return);
        }

        /**
         * @return string
         */
        public function getMethode()
        {
            return $this->_methode;
        }

        /**
         * @return array
         */
        public function getArguments()
        {
            return $this->_arguments;
        }

        /**
         * @return mixed
         */
        public function getReturnValeur()
        {
            return $this->_returnValeur;
        }

        /**
         * @param array $arguments
         */
        public function setArguments($arguments)
        {
            $this->_arguments = $arguments;
        }

        /**
         * @param string $methode
         */
        public function setMethode($methode)
        {
            $this->_methode = $methode;
        }

        /**
         * @param mixed $returnValeur
         */
        public function setReturnValeur($returnValeur)
        {
            $this->_returnValeur = $returnValeur;
        }

        private function toPlainVar($element)
        {
            if (is_bool($element))
            {
                $var = self::$boolArray[$element];
            }
            elseif (is_array($element))
            {
                $var = $this->arrayToStringPhp($element);
            }
            elseif (is_object($element))
            {
                $this->tabTricks[] = $element;
                end($this->tabTricks);
                $var = "\$this->tabTricks[" . key($this->tabTricks) . "]";
            }
            elseif (is_numeric($element))
            {
                $var = $element;
            }
            else
            {
                $var = '"' . addslashes($element) . '"';
            }

            return $var;
        }
    }