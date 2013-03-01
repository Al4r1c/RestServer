<?php
	namespace Serveur\Exceptions;

	class ErrorManager {
		/**
		 * @var \Serveur\Exceptions\Handler\ErrorHandling
		 */
		private $errorHandler;

		/**
		 * @param Handler\ErrorHandling $errorHandler
		 */
		public function setErrorHandler(\Serveur\Exceptions\Handler\ErrorHandling $errorHandler) {
			$this->errorHandler = $errorHandler;
		}

		public function setHandlers() {
			$this->errorHandler->setHandlers();
		}

		/**
		 * @return \string[]
		 */
		public function getErreurs() {
			return $this->errorHandler->getErreurs();
		}
	}