<?php
	namespace Serveur\Rest;

	use Serveur\Utils\Constante;
	use Serveur\Utils\Tools;

	class RestReponse {
		/**
		 * @var HeaderManager
		 */
		private $headerManager;

		/**
		 * @var int
		 */
		private $status = 500;

		/**
		 * @var string
		 */
		private $formatRetourDefaut;

		/**
		 * @var string[]
		 */
		private $formatsAcceptes;

		/**
		 * @var string
		 */
		private $charset;

		/**
		 * @var string
		 */
		private $contenu = '';

		/**
		 * @param HeaderManager $headerManager
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 */
		public function setHeaderManager($headerManager) {
			if(!$headerManager instanceof HeaderManager) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Rest\HeaderManager', $headerManager);
			}

			$this->headerManager = $headerManager;
		}

		/**
		 * @param \Serveur\Config\Config $configuration
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 */
		public function setConfig($configuration) {
			if(!$configuration instanceof \Serveur\Config\Config) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Config\Config', $configuration);
			}

			$this->setFormats($configuration->getConfigValeur('config.default_render'), $configuration->getConfigValeur('render'));
			$this->setCharset($configuration->getConfigValeur('config.charset'));
		}

		/**
		 * @return int
		 */
		public function getStatus() {
			return $this->status;
		}

		/**
		 * @return string
		 */
		public function getContenu() {
			return $this->contenu;
		}

		/**
		 * @return \string[]
		 */
		public function getFormatsAcceptes() {
			return $this->formatsAcceptes;
		}

		/**
		 * @return string
		 */
		public function getFormatRetourDefaut() {
			return $this->formatRetourDefaut;
		}

		/**
		 * @return string
		 */
		public function getCharset() {
			return $this->charset;
		}

		/**
		 * @param string $contenu
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 */
		public function setContenu($contenu) {
			if(!is_array($contenu)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'array', $contenu);
			}

			$this->contenu = $contenu;
		}

		/**
		 * @param int $nouveauStatus
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setStatus($nouveauStatus) {
			if(!is_int($nouveauStatus)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'int', $nouveauStatus);
			}

			if(!Tools::isValideHttpCode($nouveauStatus)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20100, 500, $nouveauStatus);
			}

			$this->status = $nouveauStatus;
		}

		/**
		 * @param string $formatRetourDefaut
		 * @param string[] $formatsAcceptes
		 */
		public function setFormats($formatRetourDefaut, $formatsAcceptes) {
			$this->setFormatsAcceptes($formatsAcceptes);

			if(array_key_exists(strtoupper($formatRetourDefaut), $formatsAcceptes)) {
				$this->setFormatRetourDefaut($formatRetourDefaut);
			} else {
				$this->setFormatRetourDefaut(key($formatsAcceptes));
				trigger_error_app(20101, $formatRetourDefaut);
			}
		}

		/**
		 * @param string $formatRetourDefaut
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setFormatRetourDefaut($formatRetourDefaut) {
			if(!is_string($formatRetourDefaut)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $formatRetourDefaut);
			}

			$this->formatRetourDefaut = $formatRetourDefaut;
		}

		/**
		 * @param string[] $formatsAcceptes
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setFormatsAcceptes($formatsAcceptes) {
			if(!is_array($formatsAcceptes)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'array', $formatsAcceptes);
			}

			if(isNull($formatsAcceptes)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20102, 400);
			}

			$this->formatsAcceptes = $formatsAcceptes;
		}

		/**
		 * @param string $charset
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setCharset($charset) {
			if(!is_string($charset)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $charset);
			}

			if(!in_array(strtoupper($charset), array_map('strtoupper', mb_list_encodings()))) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20103, 500, $charset);
			}

			$this->charset = strtolower($charset);
		}

		/**
		 * @param string $formatRetour
		 */
		private function envoyerHeaders($formatRetour) {
			http_response_code($this->status);
			$this->headerManager->ajouterHeader('Content-type', Constante::chargerConfig('mimes')[strtolower($formatRetour)] . '; charset=' . strtolower($this->charset));
			$this->headerManager->envoyerHeaders();
		}

		private function getFormatRetour(array $formatsDemandes, array $formatsAcceptes, $formatDefaut) {
			$formatRetour = null;

			foreach($formatsDemandes as $unFormatDemande) {
				if(false !== $temp = array_search_recursif($unFormatDemande, $formatsAcceptes)) {
					$formatRetour = array(ucfirst(strtolower($temp)) => $unFormatDemande);
					break;
				}
			}

			if(isNull($formatRetour)) {
				if(!isNull($formatDefaut) && array_key_exists($formatDefaut, $formatsAcceptes)) {
					$formatRetour = array(ucfirst(strtolower($formatDefaut)) => $formatsAcceptes[$formatDefaut]);
				} else {
					throw new \Serveur\Exceptions\Exceptions\MainException(20104, 500, $formatDefaut);
				}
			}

			return $formatRetour;
		}

		/**
		 * @param string $renderClassName
		 * @return mixed
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		protected function getRenderClass($renderClassName) {
			if(!class_exists($view_name = '\\' . SERVER_NAMESPACE . '\Renderers\\' . $renderClassName)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(20105, 415, $renderClassName);
			}

			return new $view_name();
		}

		/**
		 * @param array $formatsDemandes
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @return string
		 */
		public function fabriquerReponse($formatsDemandes) {
			if(!is_array($formatsDemandes)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'array', $formatsDemandes);
			}

			$formatRetour = $this->getFormatRetour($formatsDemandes, $this->formatsAcceptes, $this->formatRetourDefaut);

			/* @var $view \Serveur\Renderers\AbstractRenderer */
			$view = $this->getRenderClass(key($formatRetour));

			$this->envoyerHeaders(reset($formatRetour));

			return $view->render($this->contenu);
		}
	}