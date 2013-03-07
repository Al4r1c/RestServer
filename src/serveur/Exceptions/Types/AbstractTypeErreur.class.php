<?php
    namespace Serveur\Exceptions\Types;

    abstract class AbstractTypeErreur {
        /**
         * @var string
         */
        protected $_message;

        /**
         * @var int
         */
        protected $_codeErreur;

        /**
         * @var \DateTime
         */
        protected $_date;

        /**
         * @var array
         */
        protected $_arguments;

        /**
         * @param int $erreurNum
         * @param array $arguments
         */
        public function __construct($erreurNum, $arguments = array()) {
            $this->setCode($erreurNum);
            $this->setDate(time());
            $this->_arguments = $arguments;
        }

        /**
         * @return int
         */
        public function getCodeErreur() {
            return $this->_codeErreur;
        }

        /**
         * @return string
         */
        public function getMessage() {
            if (isNull($this->_message)) {
                return '{errorMessage.' . $this->_codeErreur . '}';
            } else {
                return $this->_message;
            }
        }

        /**
         * @return array
         */
        public function getArguments() {
            return $this->_arguments;
        }

        /**
         * @return \DateTime
         */
        public function getDate() {
            return $this->_date;
        }

        /**
         * @param int $erreurNum
         */
        public function setCode($erreurNum) {
            $this->_codeErreur = $erreurNum;
        }

        /**
         * @param string $nouveauMessage
         */
        public function setMessage($nouveauMessage) {
            $this->_message = $nouveauMessage;
        }

        /**
         * @param int $timestamp
         */
        public function setDate($timestamp) {
            $this->_date = new \DateTime();
            $this->_date->setTimestamp($timestamp);
        }
    }