<?php
namespace Serveur\Traitement\Data;

use Serveur\Lib\ObjetReponse;
use Serveur\Traitement\DonneeRequete\ParametresManager;

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
     * @param ParametresManager $filtres
     * @return ObjetReponse
     */
    public function recuperer($filtres);

    /**
     * @param ParametresManager $champs
     * @return ObjetReponse
     */
    public function inserer($champs);

    /**
     * @param string $idObjet
     * @param ParametresManager $champs
     * @return ObjetReponse
     */
    public function insererIdempotent($idObjet, $champs);

    /**
     * @param string $id
     * @param ParametresManager $champs
     * @return ObjetReponse
     */
    public function mettreAJourId($id, $champs);

    /**
     * @param array $filtres
     * @param ParametresManager $champs
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
     * @param array $id
     * @param string $nomCollection
     * @param ParametresManager $nouveauxChamps
     * @return ObjetReponse
     */
    public function setCollection($id, $nomCollection, $nouveauxChamps);

    /**
     * @param array $id
     * @param string $nomCollection
     * @param string $idNouvelObjet
     * @return ObjetReponse
     */
    public function ajouterDansCollection($id, $nomCollection, $idNouvelObjet);

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