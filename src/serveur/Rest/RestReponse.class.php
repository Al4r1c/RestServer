<?php
	namespace Serveur\Rest;

	use Serveur\Utils\Constante;
	use Serveur\Utils\Tools;
	use Serveur\Exceptions\Exceptions\RestReponseException;

	class RestReponse {
		/** @var HeaderManager */
		private $headerManager;
		private $status = 500;
		private $formatRetourDefaut;
		private $formatsAcceptes;
		private $charset;
		private $contenu = '';

		public function setHeaderManager(HeaderManager $headerManager) {
			$this->headerManager = $headerManager;
		}

		public function setConfig(\Serveur\Config\Config $configuration) {
			$this->setFormats($configuration->getConfigValeur('config.default_render'), $configuration->getConfigValeur('render'));
			$this->setCharset($configuration->getConfigValeur('config.charset'));
		}

		public function getStatus() {
			return $this->status;
		}

		public function getContenu() {
			return $this->contenu;
		}

		public function getFormatsAcceptes() {
			return $this->formatsAcceptes;
		}

		public function getFormatRetourDefaut() {
			return $this->formatRetourDefaut;
		}

		public function getCharset() {
			return $this->charset;
		}

		public function setContenu($contenu) {
			if(!is_array($contenu)) {
				throw new RestReponseException(20100, 500);
			}

			$this->contenu = $contenu;
		}

		public function setStatus($nouveauStatus) {
			if(!Tools::isValideHttpCode($nouveauStatus)) {
				throw new RestReponseException(20101, 500, $nouveauStatus);
			}

			$this->status = $nouveauStatus;
		}

		public function setFormats($formatRetourDefaut, array $formatsAcceptes) {
			$this->setFormatsAcceptes($formatsAcceptes);

			if(array_key_exists(strtoupper($formatRetourDefaut), $formatsAcceptes)) {
				$this->setFormatRetourDefaut($formatRetourDefaut);
			} else {
				$this->setFormatRetourDefaut(key($formatsAcceptes));
				trigger_notice_apps(20102, $formatRetourDefaut);
			}
		}

		public function setFormatRetourDefaut($formatRetourDefaut) {
			if (!is_string($formatRetourDefaut)) {
				throw new RestReponseException(20103, 500);
			}

			$this->formatRetourDefaut = $formatRetourDefaut;
		}

		public function setFormatsAcceptes($formatsAcceptes) {
			if (!is_array($formatsAcceptes) || isNull($formatsAcceptes)) {
				throw new RestReponseException(20104, 400);
			}

			$this->formatsAcceptes = $formatsAcceptes;
		}

		public function setCharset($charset) {
			if(!in_array(strtoupper($charset), array_map('strtoupper', mb_list_encodings()))) {
				throw new RestReponseException(20105, 500, $charset);
			}

			$this->charset = strtolower($charset);
		}

		private function envoyerHeaders($formatRetour) {
			http_response_code($this->status);
			$this->headerManager->ajouterHeader('Content-type', Constante::chargerConfig('mimes')[strtolower($formatRetour)].'; charset='.strtolower($this->charset));
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

			if (isNull($formatRetour)) {
				if(!isNull($formatDefaut) && array_key_exists($formatDefaut, $formatsAcceptes)) {
					$formatRetour = array(ucfirst(strtolower($formatDefaut)) => $formatsAcceptes[$formatDefaut]);
				} else {
					throw new RestReponseException(20106, 500, $formatDefaut);
				}
			}

			return $formatRetour;
		}


		private function getRenderClass($renderClassName) {
			if(class_exists($view_name = '\\'.SERVER_NAMESPACE.'\Renderers\\'.$renderClassName)) {
				return new $view_name();
			} else {
				throw new RestReponseException(20107, 415, $renderClassName);
			}
		}

		/* @var $view \Serveur\Renderers\AbstractRenderer */
		public function fabriquerReponse(array $formatsDemandes) {
			$formatRetour = $this->getFormatRetour($formatsDemandes, $this->formatsAcceptes, $this->formatRetourDefaut);

			/* @var $view \Serveur\Renderers\AbstractRenderer */
			$view = $this->getRenderClass(key($formatRetour));

			$this->envoyerHeaders(reset($formatRetour));

			return $view->render($this->contenu);
		}
	}