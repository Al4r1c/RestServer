<?php
    namespace Serveur\Traitement;

    use Serveur\Requete\RequeteManager;
    use Serveur\Traitement\Ressource\AbstractRessource;
    use Serveur\Lib\ObjetReponse;
    use Serveur\GestionErreurs\Exceptions\MainException;

    class TraitementManager
    {
        /**
         * @param RequeteManager $requete
         * @throws MainException
         * @return ObjetReponse
         */
        public function traiterRequeteEtRecupererResultat($requete)
        {
            /** @var $ressourceObjet AbstractRessource */
            if (($ressourceObjet = $this->getRessourceClass($requete->getUriVariable(0))) !== false) {
                switch (strtoupper($requete->getMethode())) {
                    case 'GET':
                        $objetReponse =
                            $ressourceObjet->doGet(
                                $requete->getUriVariable(1),
                                $requete->getParametres(),
                                $requete->getUriVariable(2)
                            );
                        break;
                    case 'POST':
                        $objetReponse = $ressourceObjet->doPost($requete->getUriVariable(1), $requete->getParametres());
                        break;
                    case 'PUT':
                        $objetReponse = $ressourceObjet->doPut($requete->getUriVariable(1), $requete->getParametres());
                        break;
                    case 'DELETE':
                        $objetReponse = $ressourceObjet->doDelete($requete->getUriVariable(1));
                        break;
                    default:
                        throw new MainException(30000, 500, $requete->getMethode());
                        break;
                }
            } else {
                $objetReponse = new \Serveur\Lib\ObjetReponse();
                $objetReponse->setErreurHttp(404);
            }

            return $objetReponse;
        }

        /**
         * @param $nomRessourceDemandee
         * @return AbstractRessource
         * @codeCoverageIgnore
         */
        protected function getRessourceClass($nomRessourceDemandee)
        {
            if (class_exists($classeRessource = '\\Ressource\\' . ucfirst(strtolower($nomRessourceDemandee)))) {
                return new $classeRessource();
            } else {
                return false;
            }
        }
    }