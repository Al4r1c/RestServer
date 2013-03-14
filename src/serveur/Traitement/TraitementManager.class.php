<?php
    namespace Serveur\Traitement;

    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
    use Serveur\GestionErreurs\Exceptions\MainException;
    use Serveur\Lib\ObjetReponse;
    use Serveur\Requete\RequeteManager;
    use Serveur\Traitement\Ressource\AbstractRessource;

    class TraitementManager
    {
        /**
         * @var callable
         */
        private $_factoryRessource;

        /**
         * @param callable $callableFactoryRessource
         * @throws \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         */
        public function setFactoryRessource($callableFactoryRessource)
        {
            if (!is_callable($callableFactoryRessource)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'callable', $callableFactoryRessource);
            }

            $this->_factoryRessource = $callableFactoryRessource;
        }

        public function recupererNouvelleInstanceRessource($nomRessource)
        {
            if (isNull($this->_factoryRessource)) {
                throw new MainException(30001, 500);
            }

            return call_user_func($this->_factoryRessource, $nomRessource);
        }

        /**
         * @param RequeteManager $requete
         * @throws MainException
         * @return ObjetReponse
         */
        public function traiterRequeteEtRecupererResultat($requete)
        {
            /** @var $ressourceObjet AbstractRessource */
            if (($ressourceObjet = $this->recupererNouvelleInstanceRessource($requete->getUriVariable(0))) !== false) {
                switch (strtoupper($requete->getMethode())) {
                    case 'GET':
                        $objetReponse = $ressourceObjet->doGet(
                            $requete->getUriVariable(1),
                            $requete->getParametres(),
                            $requete->getUriVariable(2)
                        );
                        break;
                    case 'POST':
                        $objetReponse = $ressourceObjet->doPost($requete->getParametres());
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
                $objetReponse = new ObjetReponse();
                $objetReponse->setErreurHttp(404);
            }

            return $objetReponse;
        }
    }