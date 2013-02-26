<?php
	namespace Serveur\Exceptions\Displayer;

	abstract class AbstractDisplayer {
		protected $tradManager;

		public function __construct(\Serveur\I18n\TradManager $tradManager) {
			$this->tradManager = $tradManager;
		}

		public function ecrireMessages(array $tabErreurs) {
			if (!isNull($tabErreurs)) {
				$this->envoyerVersDestination($tabErreurs);
			}
		}

		protected function traduireMessageEtRemplacerVariables($message, array $arguments = array()) {
			return vsprintf($this->tradManager->recupererChaineTraduite($message), $arguments);
		}

		abstract protected function envoyerVersDestination(array $tabErreurs);
	}