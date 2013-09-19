<?php
namespace AlaroxRestServeur\Serveur\Traitement\DonneeRequete;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

class ParametresManager
{
    /**
     * @var ChampRequete[]
     */
    private $_champsRequete = array();

    /**
     * @var Tri[]
     */
    private $_tris = array();

    /**
     * @var mixed
     */
    private $_lazyLoad = false;

    /**
     * @var array
     */
    private static $_motsClef = array('orderBy', 'orderWay', 'pageSize', 'pageNum');

    /**
     * @return ChampRequete[]
     */
    public function getChampsRequete()
    {
        return $this->_champsRequete;
    }

    /**
     * @return mixed
     */
    public function getLazyLoad()
    {
        return $this->_lazyLoad;
    }

    /**
     * @param string $clef
     * @return ChampRequete
     */
    public function getUnChampsRequete($clef)
    {
        foreach ($this->_champsRequete as $unChampRequete) {
            if (strcmp(strtolower($clef), strtolower($unChampRequete->getChamp())) == 0) {
                return $unChampRequete;
            }
        }

        return null;
    }

    /**
     * @return Tri[]
     */
    public function getTris()
    {
        return $this->_tris;
    }

    /**
     * @param string $clef
     * @return Tri
     */
    public function getUnTri($clef)
    {
        foreach ($this->_tris as $unTri) {
            if (strcmp(strtolower($clef), strtolower($unTri->getTypeTri())) == 0) {
                return $unTri;
            }
        }

        return null;
    }

    /**
     * @param ChampRequete $donneesRequete
     * @throws ArgumentTypeException
     */
    public function addChampRequete($donneesRequete)
    {
        if (!$donneesRequete instanceof ChampRequete) {
            throw new ArgumentTypeException(
                500, '\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\DonneeRequete', $donneesRequete
            );
        }

        $this->_champsRequete[] = $donneesRequete;
    }

    /**
     * @param Tri $tri
     * @throws ArgumentTypeException
     */
    public function addTri($tri)
    {
        if (!$tri instanceof Tri) {
            throw new ArgumentTypeException(
                500, '\AlaroxRestServeur\Serveur\Traitement\DonneeRequete\Tri', $tri
            );
        }

        $this->_tris[] = $tri;
    }

    /**
     * @param array $tabParametres
     */
    public function parseTabParametres($tabParametres)
    {
        foreach ($tabParametres as $clef => $valeurParametre) {
            if ($clef == 'lazyLoad') {
                if ($valeurParametre == 'true') {
                    $this->_lazyLoad = true;
                } else {
                    $this->_lazyLoad = $valeurParametre;
                }
            } elseif (in_array($clef, self::$_motsClef, true)) {
                $this->traiterTri($clef, $valeurParametre);
            } else {
                $this->traiterNouveauChampRequete($clef, $valeurParametre);
            }
        }
    }

    /**
     * @param string $clef
     * @param string $uneCondition
     */
    private function traiterNouveauChampRequete($clef, $uneCondition)
    {
        $unChampRequete = new ChampRequete();

        if (strpos($clef, '!') !== false) {
            $tabClef = explode('!', $clef);
            $clef = $tabClef[0];

            $unChampRequete->setOperateur($this->traiterOperateur($tabClef[1]));
        }

        if (!is_array($uneCondition) && strpos($uneCondition, '|') !== false) {
            $uneCondition = explode('|', $uneCondition);
        }

        $unChampRequete->setChamp($clef);
        $unChampRequete->setValeurs($uneCondition);

        $this->addChampRequete($unChampRequete);
    }

    /**
     * @param string $clef
     * @param string $uneCondition
     */
    private function traiterTri($clef, $uneCondition)
    {
        $unTri = new Tri();

        $unTri->setTypeTri($clef);
        $unTri->setValeur($uneCondition);

        $this->_tris[] = $unTri;
    }

    /**
     * @param string $nomOperateur
     * @return array
     */
    private function traiterOperateur($nomOperateur)
    {
        $operator = new Operateur();
        $operator->setType($nomOperateur);

        return $operator;
    }
}