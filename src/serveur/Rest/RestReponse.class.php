<?php
	namespace Serveur\Rest;

	use Serveur\Utils\Constante;
	use Serveur\Utils\Tools;
	use Serveur\Exceptions\Exceptions\RestReponseException;

	class RestReponse {
		private $status = 500;
		private $formatRetourDefaut;
		private $formatsAcceptes;
		private $charset;
		private $contenu = '';

		public function setConfig(\Serveur\Config\Config $configuration) {
			$this->charset = $configuration->getConfigValeur('config.charset');
			$this->formatRetourDefaut = $configuration->getConfigValeur('config.default_render');
			$this->formatsAcceptes = $configuration->getConfigValeur('render');
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
		public function setStatus($nouveauStatus) {
			if(!Tools::isValideHttpCode($nouveauStatus)) {
				throw new RestReponseException(20100, 500, $nouveauStatus);
			}

			$this->status = $nouveauStatus;
		}

		public function setContenu($contenu) {
			$this->contenu = $contenu;
		}

		private function recupererHeader($formatRetour) {
			header('HTTP/1.1 ' . $this->status . ' ' . Constante::chargerConfig('httpcode')[$this->status][0]);
			header('Content-type: ' . Constante::chargerConfig('mimes')[strtolower($formatRetour)].'; charset='.strtolower($this->charset));
		}

		private function getFormatRetour(array $formatsDemandes, array $formatsAcceptes, $formatDefaut) {
			$formatRetour = null;

			foreach($formatsDemandes as $unFormatDemande) {
				if(false !== $temp = array_search_recursif($unFormatDemande, $formatsAcceptes)) {
					$formatRetour = array($temp, $unFormatDemande);
					break;
				}
			}

			if (isNull($formatRetour)) {
				if(!isNull($formatDefaut) && array_key_exists($formatDefaut, $formatsAcceptes)) {
					$formatRetour = array($formatDefaut, $formatsAcceptes[$formatDefaut]);
				} else {
					$key = key($formatsAcceptes);
					$valeur = $this->getFormatsAcceptes();

					while(is_array($valeur)) {
						$valeur = reset($valeur);
					}

					$formatRetour = array($key, $valeur);
				}
			}

			return $formatRetour;
		}

		public function fabriquerReponse(array $formatsDemandes) {
			$formatRetour = $this->getFormatRetour($formatsDemandes, $this->formatsAcceptes, $this->formatRetourDefaut);

			$this->recupererHeader($formatRetour[1]);

			if(class_exists($view_name = '\\'.SERVER_NAMESPACE.'\Renderers\\'.ucfirst(strtolower($formatRetour[0])))) {
				/* @var $view \Serveur\Renderers\AbstractRenderer */
				$view = new $view_name();
				$this->contenu = $view->render($this->contenu);
			} else {
				throw new RestReponseException(20101, 415, $formatRetour[0]);
			}
		}

		public function __toString() {
			return $this->contenu;
		}
	}