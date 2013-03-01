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

		public function getErreurs() {
			return $this->erreurs;
		}

		public function getErreursEtFlush() {
			$tabErreurs = $this->erreurs;
			$this->erreurs = array();

			return $tabErreurs;
		}

		public function global_ajouterErreur($erreurNumber, $codeErreur) {
			switch($erreurNumber) {
				case E_USER_ERROR:
					$this->erreurs[] = new Error($codeErreur, null, array_slice(func_get_args(), 2));
					break;

				case E_USER_NOTICE:
					$this->erreurs[] = new Notice($codeErreur, null, array_slice(func_get_args(), 2));
					break;

				default:
					die('Error type not supported.');
					break;
			}
		}

		public function exceptionHandler(\Exception $exception) {
			echo 'exception :)';
		}

		public function errorHandler($errno, $errstr, $errfile, $errline) {
			if(!(error_reporting() & $errno)) {
				return null;
			}

			switch($errno) {
				case E_COMPILE_ERROR:
				case E_ERROR:
				case E_CORE_ERROR:
				case E_USER_ERROR:
				case E_PARSE:
					$this->erreurs[] = new Error($errno, '{trad.file}: ' . $errfile . ', {trad.line}: ' . $errline . ' | {trad.warning}: ' . $errstr);
					throw new \Exception();
					break;

				case E_WARNING:
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
				case E_USER_WARNING:
				case E_NOTICE:
				case E_USER_NOTICE:
				case E_STRICT:
				case E_DEPRECATED:
				case E_USER_DEPRECATED:
				case E_RECOVERABLE_ERROR:
					$this->erreurs[] = new Notice($errno, '{trad.file}: ' . $errfile . ', {trad.line}: ' . $errline . ' | {trad.warning}: ' . $errstr);
					break;

				default:
					echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
					break;
			}

			return true;
		}
	}