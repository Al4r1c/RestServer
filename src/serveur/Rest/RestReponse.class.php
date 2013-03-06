<?php
    namespace Serveur\Rest;

    use Serveur\Utils\Constante;
    use Serveur\Utils\Tools;
    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class RestReponse {
        /**
         * @var HeaderManager
         */
        private $_headerManager;

        /**
         * @var int
         */
        private $_status = 500;

        /**
         * @var string
         */
        private $_formatRetour;

        /**
         * @var string[]
         */
        private $_formatsAcceptes;

        /**
         * @var string
         */
        private $_charset;

        /**
         * @var string
         */
        private $_contenu = '';

        /**
         * @param HeaderManager $headerManager
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function setHeaderManager($headerManager) {
            if (!$headerManager instanceof HeaderManager) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\HeaderManager', $headerManager);
            }

            $this->_headerManager = $headerManager;
        }

        /**
         * @param \Serveur\Config\Config $configuration
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function setConfig($configuration) {
            if (!$configuration instanceof \Serveur\Config\Config) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Config\Config', $configuration);
            }

            $this->setFormats($configuration->getConfigValeur('config.default_render'),
                $configuration->getConfigValeur('render'));
            $this->setCharset($configuration->getConfigValeur('config.charset'));
        }

        /**
         * @return int
         */
        public function getStatus() {
            return $this->_status;
        }

        /**
         * @return string
         */
        public function getContenu() {
            return $this->_contenu;
        }

        /**
         * @return \string[]
         */
        public function getFormatsAcceptes() {
            return $this->_formatsAcceptes;
        }

        /**
         * @return string
         */
        public function getFormatRetour() {
            return $this->_formatRetour;
        }

        /**
         * @return string
         */
        public function getCharset() {
            return $this->_charset;
        }

        /**
         * @param string $contenu
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function setContenu($contenu) {
            if (!is_array($contenu)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $contenu);
            }

            $this->_contenu = $contenu;
        }

        /**
         * @param int $nouveauStatus
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setStatus($nouveauStatus) {
            if (!is_int($nouveauStatus)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'int', $nouveauStatus);
            }

            if (!Tools::isValideHttpCode($nouveauStatus)) {
                throw new MainException(20100, 500, $nouveauStatus);
            }

            $this->_status = $nouveauStatus;
        }

        /**
         * @param string $formatRetourDefaut
         * @param string[] $formatsAcceptes
         */
        public function setFormats($formatRetourDefaut, $formatsAcceptes) {
            $this->setFormatsAcceptes($formatsAcceptes);

            if (array_key_exists(strtoupper($formatRetourDefaut), $formatsAcceptes)) {
                $this->setFormatRetour($formatRetourDefaut);
            } else {
                $this->setFormatRetour(key($formatsAcceptes));
                trigger_error_app(20101, $formatRetourDefaut);
            }
        }

        /**
         * @param string $formatRetourDefaut
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setFormatRetour($formatRetourDefaut) {
            if (!is_string($formatRetourDefaut)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $formatRetourDefaut);
            }

            $this->_formatRetour = $formatRetourDefaut;
        }

        /**
         * @param string[] $formatsAcceptes
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setFormatsAcceptes($formatsAcceptes) {
            if (!is_array($formatsAcceptes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $formatsAcceptes);
            }

            if (isNull($formatsAcceptes)) {
                throw new MainException(20102, 400);
            }

            $this->_formatsAcceptes = $formatsAcceptes;
        }

        /**
         * @param string $charset
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setCharset($charset) {
            if (!is_string($charset)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $charset);
            }

            if (!in_array(strtoupper($charset), array_map('strtoupper', mb_list_encodings()))) {
                throw new MainException(20103, 500, $charset);
            }

            $this->_charset = strtolower($charset);
        }

        private function envoyerHeaders() {
            http_response_code($this->_status);
            $this->_headerManager->ajouterHeader('Content-type',
                Constante::chargerConfig('mimes')[strtolower($this->_formatRetour)] . '; charset=' .
                strtolower($this->_charset));
            $this->_headerManager->envoyerHeaders();
        }

        private function trouverFormatRetourCorrect(array $formatsDemandes, array $formatsAcceptes, $formatDefaut) {
            $nomClassFormatRetour = null;

            foreach ($formatsDemandes as $unFormatDemande) {
                if (false !== $temp = array_search_recursif($unFormatDemande, $formatsAcceptes)) {
                    $this->_formatRetour = $unFormatDemande;
                    $nomClassFormatRetour = ucfirst(strtolower($temp));
                    break;
                }
            }

            if (isNull($nomClassFormatRetour)) {
                if (!isNull($formatDefaut) && array_key_exists($formatDefaut, $formatsAcceptes)) {
                    $this->_formatRetour = $formatsAcceptes[$formatDefaut];
                    $nomClassFormatRetour = ucfirst(strtolower($formatDefaut));
                } else {
                    throw new MainException(20104, 500, $formatDefaut);
                }
            }

            return $nomClassFormatRetour;
        }

        /**
         * @param string $renderClassName
         * @return mixed
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        protected function getRenderClass($renderClassName) {
            if (!class_exists($nomVue = '\\' . SERVER_NAMESPACE . '\Renderers\\' . $renderClassName)) {
                throw new MainException(20105, 415, $renderClassName);
            }

            return new $nomVue();
        }

        /**
         * @param array $formatsDemandes
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return string
         */
        public function fabriquerReponse($formatsDemandes) {
            if (!is_array($formatsDemandes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $formatsDemandes);
            }

            /* @var $view \Serveur\Renderers\AbstractRenderer */
            $view = $this->getRenderClass($this->trouverFormatRetourCorrect($formatsDemandes,
                $this->_formatsAcceptes,
                $this->_formatRetour));

            $this->envoyerHeaders();

            return $view->render($this->_contenu);
        }
    }