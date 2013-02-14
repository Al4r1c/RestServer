<?php
	namespace Serveur\Exceptions;

	class ErrorManager {
		/** @var \Serveur\Exceptions\Handler\ErrorHandling */
		private $errorHandler;
		/** @var \Serveur\Exceptions\Displayer\AbstractDisplayer */
		private $displayer;

		public function setErrorHandler(\Serveur\Exceptions\Handler\ErrorHandling $errorHandler) {
			$this->errorHandler = $errorHandler;
		}

		public function setHandlers() {
			$this->errorHandler->setHandlers();
		}

		public function setDisplayer(\Serveur\Exceptions\Displayer\AbstractDisplayer $displayer) {
			$this->displayer = $displayer;
		}

		public function ecrireErreursSurvenues() {
			if(count($tabErreurs = $this->errorHandler->getErreursEtFlush()) > 0) {
				$this->displayer->ecrireMessages($tabErreurs);

				if(count($tabErreurs = $this->errorHandler->getErreurs()) > 0) {
					$this->displayer->ecrireMessages($tabErreurs);
				}
			}
		}
	}