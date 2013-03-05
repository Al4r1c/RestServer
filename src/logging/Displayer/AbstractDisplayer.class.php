<?php
    namespace Logging\Displayer;

    abstract class AbstractDisplayer {
        /** @var \Logging\I18n\TradManager */
        protected $_tradManager;

        /**
         * @param \Logging\I18n\TradManager $tradManager
         */
        public function setTradManager($tradManager) {
            $this->_tradManager = $tradManager;
        }

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur[] $tabErreurs
         * @return void
         */
        public function ecrireMessages(array $tabErreurs) {
            if (!isNull($tabErreurs)) {
                $this->ecrireMessageErreur($tabErreurs);
            }
        }

        /**
         * @param \Serveur\Rest\RestRequete $restRequete
         * @param \Serveur\Rest\RestReponse $restReponse
         */
        public function ecrireAcessLog($restRequete, $restReponse) {
            $this->ecrireMessageAcces($restRequete, $restReponse);
        }

        /**
         * @param string $codeMessage
         * @param array $arguments
         * @return string
         */
        protected function traduireMessageEtRemplacerVariables($codeMessage, array $arguments = array()) {
            return vsprintf($this->_tradManager->recupererChaineTraduite($codeMessage), $arguments);
        }

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur[] $tabErreurs
         * @return void
         */
        abstract protected function ecrireMessageErreur(array $tabErreurs);

        /**
         * @param \Serveur\Rest\RestRequete $restRequete
         * @param \Serveur\Rest\RestReponse $restReponse
         * @return void
         */
        abstract protected function ecrireMessageAcces($restRequete, $restReponse);
    }