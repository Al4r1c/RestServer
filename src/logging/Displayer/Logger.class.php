<?php
	namespace Logging\Displayer;

	use Logging\Displayer\AbstractDisplayer;

	class Logger extends AbstractDisplayer {

		private static $nomFichier = 'errors.log';
		private $fichierLogDestination;

		public function __construct() {
			$this->fichierLogDestination = $this->creerFichierSiNexistePas(self::$nomFichier);
		}

		private function creerFichierSiNexistePas($nomFichier) {
			$fichier = \Serveur\Utils\FileManager::getFichier();
			$fichier->setFichierParametres($nomFichier, BASE_PATH . '/log');
			$fichier->creerFichier('0700');

			return $fichier;
		}

		/**
		 * @param \Serveur\Exceptions\Types\AbstractTypeErreur[] $tabErreurs
		 * @throws \Exception
		 * */
		protected function envoyerVersDestination(array $tabErreurs) {
			$fp = fopen($this->fichierLogDestination->getCheminCompletFichier(), 'a+');
			fseek($fp, SEEK_END);

			foreach($tabErreurs as $uneErreur) {
				if(substr_count(strtolower(get_class($uneErreur)), 'error') === 1) {
					$message = '{trad.fatalerror}: ' . $uneErreur->getMessage();
				} elseif(substr_count(strtolower(get_class($uneErreur)), 'notice') === 1) {
					$message = '{trad.notice}: ' . $uneErreur->getMessage();
				} else {
					throw new \Exception('Invalid error type');
				}

				fputs($fp, $uneErreur->getDate()->format('d-m-Y H:i:s') . ": \r\n");
				fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables("{trad.error}" . " n°" . $uneErreur->getCode() . ": {errorType.".substr($uneErreur->getCode(), 0, -2)."}\r\n"));
				fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables($message, $uneErreur->getArguments()) . "\r\n");
			}

			fclose($fp);
		}
	}