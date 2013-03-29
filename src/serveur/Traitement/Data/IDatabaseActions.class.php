<?php
namespace Serveur\Traitement\Data;

use Serveur\Lib\ObjetReponse;

interface IDatabaseActions
{
    /**
     * @param DatabaseConfig $databaseInformations
     */
    public function ouvrirConnectionDepuisFichier($databaseInformations);

    /**
     * @param resource $connection
     * @return bool
     */
    public function fermerConnection($connection);

    /**
     * @param string $id
     * @return ObjetReponse
     */
    public function recupererId($id);

    /**
     * @param array $filtres
     * @param array $tri
     * @return ObjetReponse
     */
    public function recuperer($filtres, $tri = array());

    /**
     * @param array $champs
     * @return ObjetReponse
     */
    public function inserer($champs);

    /**
     * @param string $idObjet
     * @param array $champs
     * @return ObjetReponse
     */
    public function insererIdempotent($idObjet, $champs);

    /**
     * @param string $id
     * @param array $champs
     * @return ObjetReponse
     */
    public function mettreAJourId($id, $champs);

    /**
     * @param array $filtres
     * @param array $champs
     * @return ObjetReponse
     */
    public function mettreAJour($filtres, $champs);

    /**
     * @param string $id
     * @return ObjetReponse
     */
    public function supprimerId($id);

    /**
     * @param array $filtres
     * @return ObjetReponse
     */
    public function supprimer($filtres = array());

    /**
     * @param string $id
     * @param string $nomCollection
     * @param string $idNouvelObjet
     * @return ObjetReponse
     */
    public function ajouterUnDansCollection($id, $nomCollection, $idNouvelObjet);

    /**
     * @param array $filtres
     * @param string $nomCollection
     * @param string $idNouvelObjet
     * @return ObjetReponse
     */
    public function ajouterDansCollection($filtres, $nomCollection, $idNouvelObjet);

    /**
     * @param string $id
     * @param string $nomCollection
     * @param string $idObjetCollection
     * @return ObjetReponse
     */
    public function supprimerDansCollection($id, $nomCollection, $idObjetCollection);

    /**
     * @param string $id
     * @param string $nomCollection
     * @return ObjetReponse
     */
    public function supprimerCollection($id, $nomCollection);
}