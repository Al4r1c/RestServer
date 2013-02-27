<?php
	namespace Serveur\Config;

	use Serveur\Exceptions\Exceptions\ConfigException;

	class Config {

		private $applicationConfiguration = array();
		private static $clefMinimales = array('config', 'config.default_lang', 'config.default_render', 'config.default_displayer', 'displayers', 'render', 'languages');

		public function chargerConfiguration(\Serveur\Lib\Fichier $fichierFramework) {
			try {
				$this->applicationConfiguration = array_change_key_case($fichierFramework->chargerFichier(), CASE_UPPER);
				$this->valider();
			} catch(\Serveur\Exceptions\Exceptions\FichierException $fe) {
				throw new ConfigException(30000, 500, $fichierFramework->getCheminCompletFichier());
			}
		}

		private function valider() {
			foreach(self::$clefMinimales as $uneClefQuiDoitExister) {
				if(is_null($this->getConfigValeur($uneClefQuiDoitExister))) {
					throw new ConfigException(30001, 500, $uneClefQuiDoitExister);
				}
			}
		}

		public function getConfigValeur($clefConfig) {
			if($valeur = rechercheValeurTableauMultidim(explode('.', strtoupper($clefConfig)), $this->applicationConfiguration)) {
				return $valeur;
			} else {
				trigger_notice_apps(30002, $clefConfig);

				return null;
			}
		}
	}