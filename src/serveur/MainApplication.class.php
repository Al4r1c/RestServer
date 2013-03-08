<?php
    namespace Serveur;

    use Serveur\Utils\Constante;
    use Serveur\GestionErreurs\Exceptions\MainException;

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
         * @var \Serveur\Requete\RequeteManager
         */
        private $_requete;

        /**
         * @var \Serveur\Reponse\ReponseManager
         */
        private $_reponse;

        /**
         * @param \Conteneur\Conteneur $nouveauConteneur
         */
        public function __construct($nouveauConteneur)
        {
            http_response_code(500);
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
                $this->_requete = $this->_conteneur->getRequeteManager();
                $this->ecrireRequeteLog();

                $this->_reponse = $this->_conteneur->getReponseManager();

                $resultat = $this->recupererResultat(200, $this->_requete->getParametres());
            }
            catch (\Exception $e) {
                $resultat = $this->leverException($e);
            }

            $this->ecrireReponseLog();

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

            return $this->recupererResultat(
                $statusHttp,
                array('Code' => $statusHttp, 'Status' => $infoHttpCode[0], 'Message' => $infoHttpCode[1])
            );
        }

        /**
         * @param int $codeHttp
         * @param array $donneessAAfficher
         * @return string
         */
        private function recupererResultat($codeHttp, $donneessAAfficher)
        {
            $this->_reponse->setStatus($codeHttp);
            $this->_reponse->setContenu($donneessAAfficher);

            return $this->_reponse->fabriquerReponse($this->_requete->getFormatsDemandes());
        }

        private function ecrireRequeteLog()
        {
            foreach ($this->_observeurs as $unObserveur) {
                $unObserveur->ecrireLogRequete(
                    $this->_requete
                );
            }
        }

        private function ecrireReponseLog()
        {
            foreach ($this->_observeurs as $unObserveur) {
                $unObserveur->ecrireLogReponse(
                    $this->_reponse
                );
            }
        }
    }