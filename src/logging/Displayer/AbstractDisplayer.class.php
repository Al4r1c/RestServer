<?php
    namespace Logging\Displayer;

    abstract class AbstractDisplayer
    {
        /** @var \Logging\I18n\TradManager */
        protected $_tradManager;

        /**
         * @param \Logging\I18n\TradManager $tradManager
         * @throws \InvalidArgumentException
         */
        public function setTradManager($tradManager)
        {
            if (!$tradManager instanceof \Logging\I18n\TradManager) {
                throw new \InvalidArgumentException();
            }

            $this->_tradManager = $tradManager;
        }

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur $uneErreur
         * @return void
         */
        public function ecrireErreurLog($uneErreur)
        {
            if (!isNull($uneErreur)) {
                $this->ecrireMessageErreur($uneErreur);
            }
        }

        /**
         * @param \Serveur\Rest\RestRequete $restRequete
         * @param \Serveur\Rest\RestReponse $restReponse
         */
        public function ecrireAcessLog($restRequete, $restReponse)
        {
            $this->ecrireMessageAcces($restRequete, $restReponse);
        }

        /**
         * @param string $messageATraduire
         * @param array $arguments
         * @return string
         */
        protected function traduireMessageEtRemplacerVariables($messageATraduire, array $arguments = array())
        {
            return vsprintf($this->_tradManager->recupererChaineTraduite($messageATraduire), $arguments);
        }

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur $uneErreur
         * @return void
         */
        abstract protected function ecrireMessageErreur($uneErreur);

        /**
         * @param \Serveur\Rest\RestRequete $restRequete
         * @param \Serveur\Rest\RestReponse $restReponse
         * @return void
         */
        abstract protected function ecrireMessageAcces($restRequete, $restReponse);
    }