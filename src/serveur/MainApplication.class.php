<?php
namespace Serveur;

use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Lib\ObjetReponse;
use Serveur\Requete\RequeteManager;
use Serveur\Utils\Constante;

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
            $requete = $this->_conteneur->getRequeteManager();
            $this->ecrireRequeteLog($requete);

            $traitementRequete = $this->_conteneur->getTraitementManager();
            $contenu = $this->fabriquerEtRecupererReponse(
                $traitementRequete->traiterRequeteEtRecupererResultat($requete), $requete->getFormatsDemandes()
            );
        } catch (MainException $e) {
            $contenu = $this->fabriquerEtRecupererReponse($e->getObjetReponseErreur());
        }

        return $contenu;
    }

    /**
     * @param \Serveur\Lib\ObjetReponse $objetReponse
     * @param array $formatsDemandees
     * @return \Serveur\Reponse\ReponseManager
     */
    private function fabriquerEtRecupererReponse($objetReponse, $formatsDemandees = array())
    {
        $reponse = $this->_conteneur->getReponseManager();
        $reponse->setObserveurs($this->_observeurs);
        $reponse->fabriquerReponse($objetReponse, $formatsDemandees);

        return $reponse->getContenuReponse();
    }

    /**
     * @param RequeteManager $requete
     */
    private function ecrireRequeteLog($requete)
    {
        foreach ($this->_observeurs as $unObserveur) {
            $unObserveur->ecrireLogRequete($requete);
        }
    }
}