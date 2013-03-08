<?php
    namespace Serveur;

    use Serveur\Utils\Constante;
    use Serveur\Exceptions\Exceptions\MainException;

    class MainApplication
    {
        /**
         * @var \Conteneur\Conteneur
         */
        private $_conteneur;

        /**
         * @var \Logging\Displayer\AbstractDisplayer[]
         */
        private $_observeurs = array();

        /**
         * @param \Conteneur\Conteneur $nouveauConteneur
         */
        public function __construct($nouveauConteneur)
        {
            $this->_conteneur = $nouveauConteneur;
        }

        public function setHandlers()
        {
            $this->_conteneur->getErrorManager()->setHandlers();
        }

        /**
         * @param \Logging\Displayer\AbstractDisplayer $observeur
         */
        public function ajouterObserveur($observeur)
        {
            $this->_observeurs[] = $observeur;
            $this->_conteneur->getErrorManager()->ajouterObserveur($observeur);
        }

        /**
         * @return string
         */
        public function run()
        {
            try {
                $this->_conteneur->getRestManager()->setVariablesReponse(
                    200,
                    $this->_conteneur->getRestManager()->getParametres()
                );

                $resultat = $this->_conteneur->getRestManager()->fabriquerReponse();
            }
            catch (\Exception $e) {
                $resultat = $this->leverException($e);
            }

            $this->ecrireAccesLog();

            return $resultat;
        }

        /**
         * @param \Exception $uneException
         * @return string
         */
        private function leverException(\Exception $uneException)
        {
            if ($uneException instanceof MainException) {
                $statusHttp = $uneException->getStatus();
            } else {
                $statusHttp = 500;
            }

            $infoHttpCode = Constante::chargerConfig('httpcode')[$statusHttp];

            $this->_conteneur->getRestManager()->setVariablesReponse(
                $statusHttp,
                array('Code' => $statusHttp, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1])
            );

            return $this->_conteneur->getRestManager()->fabriquerReponse();
        }

        private function ecrireAccesLog()
        {
            foreach ($this->_observeurs as $unObserveur) {
                $unObserveur->ecrireAcessLog(
                    $this->_conteneur->getRestManager()->getRestRequest(),
                    $this->_conteneur->getRestManager()->getRestResponse()
                );
            }
        }
    }