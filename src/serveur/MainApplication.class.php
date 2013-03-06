<?php
    namespace Serveur;

    use Serveur\Utils\Constante;
    use Serveur\Exceptions\Exceptions\MainException;

    class MainApplication {
        /**
         * @var \Conteneur\MonConteneur
         */
        private $_conteneur;

        /**
         * @var \Logging\Displayer\AbstractDisplayer[]
         */
        private $_observeurs = array();

        /**
         * @param \Conteneur\MonConteneur $nouveauConteneur
         */
        public function __construct(\Conteneur\MonConteneur $nouveauConteneur) {
            $this->_conteneur = $nouveauConteneur;
            $this->_conteneur->getErrorManager()->setHandlers();
        }

        /**
         * @param \Logging\Displayer\AbstractDisplayer $observeur
         */
        public function ajouterObserveur(\Logging\Displayer\AbstractDisplayer $observeur) {
            $this->_observeurs[] = $observeur;
        }

        /**
         * @return string
         */
        public function run() {
            try {
                $this->_conteneur->getRestManager()
                    ->setVariablesReponse(200, $this->_conteneur->getRestManager()->getParametres());

                return $this->_conteneur->getRestManager()->fabriquerReponse();
            } catch (\Exception $e) {
                return $this->leverException($e);
            }
        }

        /**
         * @param \Exception $uneException
         * @return string
         */
        private function leverException(\Exception $uneException) {
            if ($uneException instanceof MainException) {
                $statusHttp = $uneException->getStatus();
            } else {
                $statusHttp = 500;
            }

            http_response_code($statusHttp);

            $infoHttpCode = Constante::chargerConfig('httpcode')[$statusHttp];

            $this->_conteneur->getRestManager()->setVariablesReponse($statusHttp,
                array('Code' => $statusHttp, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1]));

            return $this->_conteneur->getRestManager()->fabriquerReponse();
        }

        public function __destruct() {
            foreach ($this->_observeurs as $unObserveur) {
                foreach ($this->_conteneur->getErrorManager()->getErreurs() as $uneErreur) {
                    $unObserveur->ecrireErreurLog($uneErreur);
                }

                $unObserveur->ecrireAcessLog($this->_conteneur->getRestManager()->getRestRequest(),
                    $this->_conteneur->getRestManager()->getRestResponse());
            }
        }
    }