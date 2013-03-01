<?php
	namespace Serveur\Exceptions;

	class ErrorManager {
		/** @var \Serveur\Exceptions\Handler\ErrorHandling */
		private $errorHandler;

		public function setErrorHandler(\Serveur\Exceptions\Handler\ErrorHandling $errorHandler) {
			$this->errorHandler = $errorHandler;
		}

		public function setHandlers() {
			$this->errorHandler->setHandlers();
		}

		public function getErreurs() {
			return $this->errorHandler->getErreurs();
		}
	}