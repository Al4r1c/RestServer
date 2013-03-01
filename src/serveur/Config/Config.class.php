<?php
	namespace Serveur\Config;

	class Config {

		private $applicationConfiguration = array();
		private static $clefMinimales = array('config', 'config.default_render', 'config.default_displayer', 'displayers', 'render');

		public function chargerConfiguration(\Serveur\Lib\Fichier $fichierFramework) {
			try {
				$this->applicationConfiguration = array_change_key_case($fichierFramework->chargerFichier(), CASE_UPPER);
			} catch(\Exception $fe) {
				throw new \Serveur\Exceptions\Exceptions\MainException(30000, 500, $fichierFramework->getCheminCompletFichier());
			}

			$this->validerFichierConfiguration();
		}

		private function validerFichierConfiguration() {
			foreach(self::$clefMinimales as $uneClefQuiDoitExister) {
				if(is_null($this->getConfigValeur($uneClefQuiDoitExister))) {
					throw new \Serveur\Exceptions\Exceptions\MainException(30001, 500, $uneClefQuiDoitExister);
				}
			}
		}

		/** @return array|bool|null */
		public function getConfigValeur($clefConfig) {
			if($valeur = rechercheValeurTableauMultidim(explode('.', strtoupper($clefConfig)), $this->applicationConfiguration)) {
				return $valeur;
			} else {
				trigger_error_app(E_USER_NOTICE, 30002, $clefConfig);

				return null;
			}
		}
	}