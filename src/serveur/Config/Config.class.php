<?php
	namespace Serveur\Config;

	class Config {

		private $applicationConfiguration = array();
		private static $clefMinimales = array('config', 'config.default_render', 'config.default_displayer', 'displayers', 'render');

		/**
		 * @param \Serveur\Lib\Fichier $fichierFramework
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function chargerConfiguration(\Serveur\Lib\Fichier $fichierFramework) {
			if(!$fichierFramework instanceof \Serveur\Lib\Fichier) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Lib\Fichier', get_class($fichierFramework));
			}

			try {
				$this->applicationConfiguration = array_change_key_case($fichierFramework->chargerFichier(), CASE_UPPER);
			} catch(\Exception $fe) {
				throw new \Serveur\Exceptions\Exceptions\MainException(30000, 500, $fichierFramework->getCheminCompletFichier());
			}

			$this->validerFichierConfiguration();
		}

		/**
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		private function validerFichierConfiguration() {
			foreach(self::$clefMinimales as $uneClefQuiDoitExister) {
				if(is_null($this->getConfigValeur($uneClefQuiDoitExister))) {
					throw new \Serveur\Exceptions\Exceptions\MainException(30001, 500, $uneClefQuiDoitExister);
				}
			}
		}

		/**
		 * @param string $clefConfig
		 * @return array|bool|null
		 */
		public function getConfigValeur($clefConfig) {
			if($valeur = rechercheValeurTableauMultidim(explode('.', strtoupper($clefConfig)), $this->applicationConfiguration)) {
				return $valeur;
			} else {
				trigger_error_app(E_USER_NOTICE, 30002, $clefConfig);

				return null;
			}
		}
	}