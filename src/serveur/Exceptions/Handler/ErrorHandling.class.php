<?php
	namespace Serveur\Exceptions\Handler;

	use Serveur\Exceptions\Types\Notice;
	use Serveur\Exceptions\Types\Error;

	class ErrorHandling {

		/**
		 * @var string[]
		 */
		private $erreurs = array();

		public function setHandlers() {
			set_error_handler(array($this, 'errorHandler'));
			set_exception_handler(array($this, 'exceptionHandler'));
			$GLOBALS['global_function_appli_error'] = array($this, 'global_ajouterErreur');
		}

		/**
		 * @return \string[]
		 */
		public function getErreurs() {
			return $this->erreurs;
		}

		/**
		 * @param int $erreurNumber
		 * @param int $codeErreur
		 * @param array $arguments
		 */
		public function global_ajouterErreur($erreurNumber, $codeErreur, $arguments) {
			switch($erreurNumber) {
				case E_USER_ERROR:
					$this->erreurs[] = new Error($codeErreur, null, $arguments);
					break;

				case E_USER_NOTICE:
					$this->erreurs[] = new Notice($codeErreur, null, $arguments);
					break;

				default:
					die('Error type not supported.');
					break;
			}
		}

		/**
		 * @param \Exception $exception
		 */
		public function exceptionHandler(\Exception $exception) {

		}

		/**
		 * @param int $codeErreur
		 * @param string $messageErreur
		 * @param string $fichierErreur
		 * @param int $ligneErreur
		 * @return bool|null
		 * @throws \Exception
		 */
		public function errorHandler($codeErreur, $messageErreur, $fichierErreur, $ligneErreur) {
			if(!(error_reporting() & $codeErreur)) {
				return null;
			}

			switch($codeErreur) {
				case E_COMPILE_ERROR:
				case E_ERROR:
				case E_CORE_ERROR:
				case E_USER_ERROR:
				case E_PARSE:
					$this->erreurs[] = new Error($codeErreur, '{trad.file}: ' . $fichierErreur . ', {trad.line}: ' . $ligneErreur . ' | {trad.warning}: ' . $messageErreur);
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
					$this->erreurs[] = new Notice($codeErreur, '{trad.file}: ' . $fichierErreur . ', {trad.line}: ' . $ligneErreur . ' | {trad.warning}: ' . $messageErreur);
					break;

				default:
					echo "Type d'erreur inconnu : [$codeErreur] $messageErreur<br />\n";
					break;
			}

			return true;
		}
	}