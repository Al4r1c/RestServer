<?php
	namespace Serveur\Exceptions\Types;

	abstract class AbstractTypeErreur {

		protected $message;
		protected $codeErreur;
		/** @var \DateTime */
		protected $date;
		protected $arguments;

		public function __construct($erreurNum, $message, $arguments = array()) {
			$this->setCode($erreurNum);
			$this->recupererMessage($message);
			$this->setDate(time());
			$this->arguments = $arguments;
		}

		public function getCode() {
			return $this->codeErreur;
		}

		public function getMessage() {
			return $this->message;
		}

		public function getArguments() {
			return $this->arguments;
		}

		public function getDate() {
			return $this->date;
		}

		public function setCode($erreurNum) {
			if(!empty($erreurNum)) {
				$this->codeErreur = $erreurNum;
			}
		}

		public function setMessage($nouveauMessage) {
			$this->message = $nouveauMessage;
		}

		public function recupererMessage($message) {
			if(isNull($message)) {
				$message = '{errorMessage.' . $this->codeErreur . '}';
			}

			$this->message = $message;
		}

		public function setDate($timestamp) {
			$this->date = new \DateTime();
			$this->date->setTimestamp($timestamp);
		}
	}