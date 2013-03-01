<?php
	namespace Logging\Displayer;

	abstract class AbstractDisplayer {
		/** @var \Logging\I18n\TradManager */
		protected $tradManager;

		public function setTradManager(\Logging\I18n\TradManager $tradManager) {
			$this->tradManager = $tradManager;
		}

		public function ecrireMessages(array $tabErreurs) {
			if(!isNull($tabErreurs)) {
				$this->envoyerVersDestination($tabErreurs);
			}
		}

		protected function traduireMessageEtRemplacerVariables($codeMessage, array $arguments = array()) {
			return vsprintf($this->tradManager->recupererChaineTraduite($codeMessage), $arguments);
		}

		abstract protected function envoyerVersDestination(array $tabErreurs);
	}