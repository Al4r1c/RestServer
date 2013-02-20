<?php
	namespace Serveur;

	use Serveur\Utils\Constante;
	use Serveur\Exceptions\Exceptions\MainException;
	use Serveur\Exceptions\Exceptions\ApplicationException;

	class MainApplication {

		private $conteneur;

		public function __construct(\Conteneur\MonConteneur $nouveauConteneur) {
			$this->conteneur = $nouveauConteneur;
			$this->conteneur->getErrorManager()->setHandlers();
		}

		public function run() {
			try {
				$this->setErrorManagerDisplayer($this->conteneur->getConfigManager()->getConfigValeur('displayers.'. $this->conteneur->getConfigManager()->getConfigValeur('config.default_displayer')));

				$this->conteneur->getRestManager()->setVariablesReponse(200, $this->conteneur->getRestManager()->getParametres());
			} catch(MainException $e) {
				$this->leverException($e->getStatus());
			} catch(\Exception $e) {
				$this->leverException();
			}
		}

		public function recupererResultat() {
			try {
				return $this->conteneur->getRestManager()->fabriquerReponse();
			} catch(MainException $e) {
				return $this->leverException($e->getStatus());
			} catch(\Exception $e) {
				return $this->leverException();
			}
		}

		private function setErrorManagerDisplayer($nomClasseDisplayer) {
			if(class_exists($displayerName = '\\'.SERVER_NAMESPACE.'\Exceptions\Displayer\\'.ucfirst(strtolower($nomClasseDisplayer)))) {
				$this->conteneur->getErrorManager()->setDisplayer(new $displayerName($this->conteneur->getTradManager()));
			} else {
				throw new \Serveur\Exceptions\Exceptions\ApplicationException(10000, 500, $displayerName);
			}
		}

		private function leverException($code = 500) {
			$infoHttpCode = Constante::chargerConfig('httpcode')[$code];

			$this->conteneur->getRestManager()->setVariablesReponse($code, array('Code' => $code, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1]));

			return $this->conteneur->getRestManager()->fabriquerReponse();
		}

		public function __destruct() {
			$this->conteneur->getErrorManager()->ecrireErreursSurvenues();
		}
	}