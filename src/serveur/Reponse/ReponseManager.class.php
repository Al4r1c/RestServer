<?php
    namespace Serveur\Reponse;

    use Serveur\Utils\Constante;
    use Serveur\Lib\ObjetReponse;
    use Serveur\GestionErreurs\Exceptions\MainException;
    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

    class ReponseManager
    {
        /**
         * @var \Serveur\Reponse\Header\Header
         */
        private $_header;

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
        private $contenu;

        /**
         * @return \string[]
         */
        public function getFormatsAcceptes()
        {
            return $this->_formatsAcceptes;
        }

        /**
         * @return string
         */
        public function getFormatRetour()
        {
            return $this->_formatRetour;
        }

        /**
         * @return string
         */
        public function getCharset()
        {
            return $this->_charset;
        }

        /**
         * @codeCoverageIgnore
         */
        public function getStatus()
        {
            return http_response_code();
        }

        /**
         * @return string
         */
        public function getContenu()
        {
            return $this->contenu;
        }

        /**
         * @param \Serveur\Reponse\Header\Header $headerManager
         * @throws ArgumentTypeException
         */
        public function setHeader($headerManager)
        {
            if (!$headerManager instanceof \Serveur\Reponse\Header\Header) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Reponse\Rest\HeaderManager', $headerManager);
            }

            $this->_header = $headerManager;
        }

        /**
         * @param \Serveur\Reponse\Config\Config $configuration
         * @throws ArgumentTypeException
         */
        public function setConfig($configuration)
        {
            if (!$configuration instanceof \Serveur\Reponse\Config\Config) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Reponse\Config\Config', $configuration);
            }

            $this->setFormats(
                $configuration->getConfigValeur('config.default_render'),
                $configuration->getConfigValeur('render')
            );
            $this->setCharset($configuration->getConfigValeur('config.charset'));
        }

        /**
         * @param string $formatRetourDefaut
         * @param string[] $formatsAcceptes
         */
        public function setFormats($formatRetourDefaut, $formatsAcceptes)
        {
            $this->setFormatsAcceptes($formatsAcceptes);

            if (array_key_exists(strtoupper($formatRetourDefaut), $formatsAcceptes)) {
                $this->setFormatRetour($formatRetourDefaut);
            } else {
                $this->setFormatRetour(key($formatsAcceptes));
                trigger_error_app(40000, $formatRetourDefaut);
            }
        }

        /**
         * @param string $formatRetourDefaut
         * @throws ArgumentTypeException
         */
        public function setFormatRetour($formatRetourDefaut)
        {
            if (!is_string($formatRetourDefaut)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $formatRetourDefaut);
            }

            $this->_formatRetour = $formatRetourDefaut;
        }

        /**
         * @param string[] $formatsAcceptes
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setFormatsAcceptes($formatsAcceptes)
        {
            if (!is_array($formatsAcceptes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $formatsAcceptes);
            }

            if (isNull($formatsAcceptes)) {
                throw new MainException(40001, 400);
            }

            $this->_formatsAcceptes = $formatsAcceptes;
        }

        /**
         * @param string $charset
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setCharset($charset)
        {
            if (!is_string($charset)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $charset);
            }

            if (!in_array(strtoupper($charset), array_map('strtoupper', mb_list_encodings()))) {
                throw new MainException(40002, 500, $charset);
            }

            $this->_charset = strtolower($charset);
        }

        private function envoyerHeaders($codeHttp)
        {
            http_response_code($codeHttp);
            $this->_header->ajouterHeader(
                'Content-type',
                Constante::chargerConfig('mimes')[strtolower($this->_formatRetour)] . '; charset=' .
                strtolower($this->_charset)
            );
            $this->_header->envoyerHeaders();
        }

        private function trouverFormatRetourCorrect(array $formatsDemandes, array $formatsAcceptes, $formatDefaut)
        {
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
                    throw new MainException(40003, 500, $formatDefaut);
                }
            }

            return $nomClassFormatRetour;
        }

        /**
         * @param string $renderClassName
         * @return mixed
         * @throws MainException
         * @codeCoverageIgnore
         */
        protected function getRenderClass($renderClassName)
        {
            if (!class_exists($nomVue = '\\' . __NAMESPACE__ . '\Renderers\\' . $renderClassName)) {
                throw new MainException(40004, 415, $renderClassName);
            }

            return new $nomVue();
        }

        /**
         * @param ObjetReponse $objetReponse
         * @param array $formatsDemandes
         * @throws \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @return string
         */
        public function fabriquerReponse($objetReponse, $formatsDemandes)
        {
            if (!is_array($formatsDemandes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $formatsDemandes);
            }

            /* @var $view \Serveur\Reponse\Renderers\AbstractRenderer */
            $view = $this->getRenderClass(
                $this->trouverFormatRetourCorrect(
                    $formatsDemandes,
                    $this->_formatsAcceptes,
                    $this->_formatRetour
                )
            );

            $this->envoyerHeaders($objetReponse->getStatusHttp());

            $this->contenu = $view->render($objetReponse->getDonneesReponse());
        }
    }