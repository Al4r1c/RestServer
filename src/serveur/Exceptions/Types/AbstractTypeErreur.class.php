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
         * @param string $message
         * @param array $arguments
         */
        public function __construct($erreurNum, $message, $arguments = array()) {
            $this->setCode($erreurNum);
            $this->recupererMessage($message);
            $this->setDate(time());
            $this->_arguments = $arguments;
        }

        /**
         * @return int
         */
        public function getCode() {
            return $this->_codeErreur;
        }

        /**
         * @return string
         */
        public function getMessage() {
            return $this->_message;
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
         * @param string $message
         */
        public function recupererMessage($message) {
            if (isNull($message)) {
                $message = '{errorMessage.' . $this->_codeErreur . '}';
            }

            $this->_message = $message;
        }

        /**
         * @param int $timestamp
         */
        public function setDate($timestamp) {
            $this->_date = new \DateTime();
            $this->_date->setTimestamp($timestamp);
        }
    }