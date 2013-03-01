<?php
	namespace Conteneur;

	class Conteneur {
		/**
		 * @var string[]
		 */
		protected $conteneur;

		public function __construct() {
			$this->buildConteneur();
		}

		/**
		 * @return string[]
		 */
		public function getConteneur() {
			return $this->conteneur;
		}

		private function buildConteneur() {
			$conteneur = new \Pimple();

			$conteneur['configManager'] = $conteneur->share(function () {
				$fichier = \Serveur\Utils\FileManager::getFichier();
				$fichier->setFichierParametres('config.yaml', '/config');
				$configurationManager = new \Serveur\Config\Config();
				$configurationManager->chargerConfiguration($fichier);

				return $configurationManager;
			});

			$conteneur['server'] = function () {
				$server = new \Serveur\Rest\Server();
				$server->setVarServeur($_SERVER);

				return $server;
			};

			$conteneur['headerManager'] = function () {
				return new \Serveur\Rest\HeaderManager();
			};

			$conteneur['restRequest'] = function ($c) {
				$restRequete = new \Serveur\Rest\RestRequete();
				$restRequete->setServer($c['server']);

				return $restRequete;
			};

			$conteneur['restReponse'] = function ($c) {
				$restReponse = new \Serveur\Rest\RestReponse();
				$restReponse->setConfig($c['configManager']);
				$restReponse->setHeaderManager($c['headerManager']);

				return $restReponse;
			};

			$conteneur['restManager'] = $conteneur->share(function ($c) {
				$restManager = new \Serveur\Rest\RestManager();
				$restManager->setRequete($c['restRequest']);
				$restManager->setReponse($c['restReponse']);

				return $restManager;
			});

			$conteneur['errorManager'] = $conteneur->share(function ($c) {
				$errorManager = new \Serveur\Exceptions\ErrorManager();
				$errorManager->setErrorHandler($c['errorHandler']);

				return $errorManager;
			});

			$conteneur['errorHandler'] = function () {
				return new \Serveur\Exceptions\Handler\ErrorHandling();
			};

			$this->conteneur = $conteneur;
		}
	}