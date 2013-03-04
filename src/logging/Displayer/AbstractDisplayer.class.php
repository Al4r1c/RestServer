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
				$this->ecrireMessageErreur($tabErreurs);
			}
		}

		public function ecrireAcessLog($restRequete, $restReponse) {
			$this->ecrireMessageAcces($restRequete, $restReponse);
		}

		protected function traduireMessageEtRemplacerVariables($codeMessage, array $arguments = array()) {
			return vsprintf($this->tradManager->recupererChaineTraduite($codeMessage), $arguments);
		}

		abstract protected function ecrireMessageErreur(array $tabErreurs);
		abstract protected function ecrireMessageAcces($restRequete, $restReponse);
	}