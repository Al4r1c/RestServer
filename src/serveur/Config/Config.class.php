<?php
	namespace Serveur\Config;

	use Serveur\Lib\Spyc;
	use Serveur\Exceptions\Exceptions\ConfigException;

	class Config {

		private $applicationConfiguration = array();

		public function chargerConfiguration(\Serveur\Lib\Fichier $fichierFramework) {
			if($fichierFramework->existe()) {
				$this->applicationConfiguration = array_change_key_case($fichierFramework->charger(), CASE_UPPER);

				$this->applicationConfiguration['CONFIG']['LOG_CLASS'] = $this->applicationConfiguration['DISPLAYERS'][$this->applicationConfiguration['CONFIG']['LOG_TYPE']];
			} else {
				throw new ConfigException(30000, 500, $fichierFramework->getLocationFichier());
			}
		}

		public function getConfigValeur($clefConfig) {
			if($valeur = rechercheValeurTableauMultidim(explode('.', strtoupper($clefConfig)), $this->applicationConfiguration)) {
				return $valeur;
			} else {
				return null;
			}
		}
	}