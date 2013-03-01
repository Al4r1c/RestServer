<?php
	namespace Serveur;

	use Serveur\Utils\Constante;
	use Serveur\Exceptions\Exceptions\MainException;

	class MainApplication {

		private $conteneur;
		/** @var \Logging\Displayer\AbstractDisplayer[] */
		private $observeurs = array();

		public function __construct(\Conteneur\MonConteneur $nouveauConteneur) {
			$this->conteneur = $nouveauConteneur;
			$this->conteneur->getErrorManager()->setHandlers();
		}

		public function ajouterObserveur(\Logging\Displayer\AbstractDisplayer $observeur) {
			$this->observeurs[] = $observeur;
		}

		public function run() {
			try {
				$this->conteneur->getRestManager()->setVariablesReponse(200, $this->conteneur->getRestManager()->getParametres());

				return $this->conteneur->getRestManager()->fabriquerReponse();
			} catch(\Exception $e) {
				return $this->leverException($e);
			}
		}

		private function leverException(\Exception $uneException) {
			if($uneException instanceof MainException) {
				$statusHttp = $uneException->getStatus();
			} else {
				$statusHttp = 500;
			}

			http_response_code($statusHttp);

			$infoHttpCode = Constante::chargerConfig('httpcode')[$statusHttp];

			$this->conteneur->getRestManager()->setVariablesReponse($statusHttp, array('Code' => $statusHttp, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1]));

			return $this->conteneur->getRestManager()->fabriquerReponse();
		}

		public function __destruct() {
			foreach($this->observeurs as $unObserveur) {
				$unObserveur->ecrireMessages($this->conteneur->getErrorManager()->getErreurs());
			}
		}
	}