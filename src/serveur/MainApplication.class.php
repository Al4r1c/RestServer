<?php
namespace Serveur;

use Logging\Displayer\AbstractDisplayer;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Lib\ObjetReponse;
use Serveur\Utils\Constante;

class MainApplication
{
    /**
     * @var \Conteneur\Conteneur
     */
    private $_conteneur;

    /**
     * @var AbstractDisplayer[]
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
     * @param AbstractDisplayer $observeur
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
            $requete = $this->_conteneur->getRequeteManager();
            $requete->logRequete($this->_observeurs);

            $traitementRequete = $this->_conteneur->getTraitementManager();
            $contenu = $this->fabriquerEtRecupererReponse(
                $traitementRequete->traiterRequeteEtRecupererResultat($requete), $requete->getFormatsDemandes()
            );
        } catch (MainException $e) {
            $contenu = $this->fabriquerEtRecupererReponse($e->getObjetReponseErreur(), array('txt'));
        }

        return $contenu;
    }

    /**
     * @param ObjetReponse $objetReponse
     * @param array $formatsDemandees
     * @return string
     */
    private function fabriquerEtRecupererReponse($objetReponse, $formatsDemandees)
    {
        $reponse = $this->_conteneur->getReponseManager();
        $reponse->setObserveurs($this->_observeurs);
        $reponse->fabriquerReponse($objetReponse, $formatsDemandees);

        return $reponse->getContenuReponse();
    }
}