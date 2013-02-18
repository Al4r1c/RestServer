<?php
	namespace Serveur\Exceptions\Handler;

	use Serveur\Exceptions\Types\Notice;
	use Serveur\Exceptions\Types\Error;

	class ErrorHandling {
		private $erreurs = array();

		public function setHandlers() {
			set_error_handler(array($this, 'errorHandler'));
			set_exception_handler(array($this, 'exceptionHandler'));
			$GLOBALS['global_function_appli_error'] = array($this, 'global_ajouterErreur');
		}

		public function global_ajouterErreur() {
			$this->erreurs[] = func_get_arg(0);
		}

		public function getErreurs() {
			return $this->erreurs;
		}

		public function getErreursEtFlush() {
			$tabErreurs = $this->erreurs;
			$this->erreurs = array();
			return $tabErreurs;
		}

		public function exceptionHandler(\Exception $exception) {
			new Error($exception->getCode(), $exception->getMessage());
		}

		public function errorHandler($errno, $errstr, $errfile, $errline) {
			if(!(error_reporting() & $errno)) {
				return null;
			}

			switch($errno) {
				case E_USER_WARNING:
				case E_USER_NOTICE:
					break;

				case E_WARNING:
					new Error($errno, '{trad.file}: ' . $errfile . ', {trad.line}: ' . $errline . ' | {trad.warning}: ' . $errstr);
					break;

				case E_NOTICE:
				case E_RECOVERABLE_ERROR:
				case E_STRICT:
					new Notice($errno, '{trad.file}: ' . $errfile . ', {trad.line}: ' . $errline . ' | {trad.warning}: ' . $errstr);
					break;

				default:
					echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
					break;
			}

			return true;
		}
	}