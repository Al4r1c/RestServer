<?php
    namespace Serveur\Exceptions\Types;

    abstract class AbstractTypeErreur {
        /**
         * @var string
         */
        protected $message;

        /**
         * @var int
         */
        protected $codeErreur;

        /**
         * @var \DateTime
         */
        protected $date;

        /**
         * @var array
         */
        protected $arguments;

        /**
         * @param int $erreurNum
         * @param string $message
         * @param array $arguments
         */
        public function __construct($erreurNum, $message, $arguments = array()) {
            $this->setCode($erreurNum);
            $this->recupererMessage($message);
            $this->setDate(time());
            $this->arguments = $arguments;
        }

        /**
         * @return int
         */
        public function getCode() {
            return $this->codeErreur;
        }

        /**
         * @return string
         */
        public function getMessage() {
            return $this->message;
        }

        /**
         * @return array
         */
        public function getArguments() {
            return $this->arguments;
        }

        /**
         * @return \DateTime
         */
        public function getDate() {
            return $this->date;
        }

        /**
         * @param int $erreurNum
         */
        public function setCode($erreurNum) {
            $this->codeErreur = $erreurNum;
        }

        /**
         * @param string $nouveauMessage
         */
        public function setMessage($nouveauMessage) {
            $this->message = $nouveauMessage;
        }

        /**
         * @param string $message
         */
        public function recupererMessage($message) {
            if (isNull($message)) {
                $message = '{errorMessage.' . $this->codeErreur . '}';
            }

            $this->message = $message;
        }

        /**
         * @param int $timestamp
         */
        public function setDate($timestamp) {
            $this->date = new \DateTime();
            $this->date->setTimestamp($timestamp);
        }
    }