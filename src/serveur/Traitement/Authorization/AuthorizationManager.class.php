<?php
namespace Serveur\Traitement\Authorization;

use AlaroxFileManager\FileManager\File;
use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Requete\RequeteManager;

class AuthorizationManager
{
    /**
     * @var bool
     */
    private $_actif = false;

    /**
     * @var int
     */
    private $_timeRequestValid = 12;

    /**
     * @var Authorization[]
     */
    private $_couplesIdClef;

    /**
     * @var array
     */
    private static $_clefMinimales = array(
        'Hours_request_valid',
        'Key_complexity',
        'Key_complexity.Min_length',
        'Key_complexity.Lower',
        'Key_complexity.Upper',
        'Key_complexity.Number',
        'Key_complexity.Special_char',
        'Authorized'
    );

    /**
     * @return bool
     */
    public function isAuthActivated()
    {
        return $this->_actif;
    }

    /**
     * @return Authorization[]
     */
    public function getAuthorizations()
    {
        return $this->_couplesIdClef;
    }

    /**
     * @param string $nomEntitee
     * @return null|Authorization
     */
    public function getUneAuthorization($nomEntitee)
    {
        $cpt = 0;
        $found = null;

        while ($cpt < count($this->_couplesIdClef) && is_null($found)) {
            if (strcmp($this->_couplesIdClef[$cpt]->getEntityId(), $nomEntitee) == 0) {
                $found = $this->_couplesIdClef[$cpt];
                break;
            }

            $cpt++;
        }

        return $found;
    }

    /**
     * @param Authorization $authorization
     * @throws ArgumentTypeException
     */
    public function addAuthorization($authorization)
    {
        if (!$authorization instanceof Authorization) {
            throw new ArgumentTypeException(
                1000, 500, __METHOD__, 'Serveur\Traitement\Authorization\Authorization', $authorization
            );
        }

        $this->_couplesIdClef[] = $authorization;
    }

    /**
     * @param int $timeActif
     * @throws ArgumentTypeException
     */
    public function setTimeRequestValid($timeActif)
    {
        if (!is_numeric($timeActif)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'int', $timeActif);
        }

        $this->_timeRequestValid = $timeActif;
    }

    /**
     * @param File $fichierAuthorization
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function chargerFichierAuthorisations($fichierAuthorization)
    {
        if (!$fichierAuthorization instanceof File) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, '\AlaroxFileManager\File', $fichierAuthorization);
        }

        try {
            $tabConf = $fichierAuthorization->loadFile();
        } catch (\Exception $fe) {
            throw new MainException(30200, 500, $fichierAuthorization->getPathToFile());
        }

        if (!array_key_multi_exist('activate', $tabConf, true)) {
            throw new MainException(30201, 500, 'activate', $fichierAuthorization->getPathToFile());
        }
        $this->_actif = $tabConf['Activate'];

        if ($this->_actif === true) {
            foreach (self::$_clefMinimales as $uneClefMinimale) {
                if (!array_key_multi_exist($uneClefMinimale, $tabConf, true)) {
                    throw new MainException(30201, 500, $uneClefMinimale, $fichierAuthorization->getPathToFile());
                }
            }

            $this->setTimeRequestValid(array_key_multi_get('Hours_request_valid', $tabConf, true));

            $this->verifierClefsPriveesValide($tabConf);
        }
    }

    /**
     * @param array $tabConf
     * @throws MainException
     */
    private function verifierClefsPriveesValide($tabConf)
    {
        if (is_array($tabCoupleClefs = array_key_multi_get('Authorized', $tabConf, true))) {
            list($tabPatternsVerifications, $tailleMini) = $this->genererPatternTestsClefs($tabConf);

            foreach ($tabCoupleClefs as $id => $clefPrivee) {
                foreach ($tabPatternsVerifications as $unPatternRequis) {
                    if (preg_match('#' . $unPatternRequis['pattern'] . '#u', $clefPrivee) != 1) {
                        throw new MainException($unPatternRequis['message'], 500, $clefPrivee, $tailleMini);
                    }
                }

                $couple = new Authorization();
                $couple->setEntityId($id);
                $couple->setClefPrivee($clefPrivee);
                $this->addAuthorization($couple);
            }
        }
    }

    /**
     * @param $tabConf
     * @return array
     * @throws \Serveur\GestionErreurs\Exceptions\MainException
     */
    private function genererPatternTestsClefs($tabConf)
    {
        $tabPatternsVerifications = array();

        if (($tailleMini = array_key_multi_get('Key_complexity.min_length', $tabConf, true)) !== false) {
            if (is_int($tailleMini) && $tailleMini >= 0) {
                $tabPatternsVerifications[] = array('pattern' => '(.){' . $tailleMini . ',}', 'message' => 30203);
            } else {
                throw new MainException(30202, 500, $tailleMini, 'Min_length');
            }
        }

        if (array_key_multi_get('Key_complexity.Lower', $tabConf, true)) {
            $tabPatternsVerifications[] = array('pattern' => '\p{Ll}', 'message' => 30204);
        }

        if (array_key_multi_get('Key_complexity.Upper', $tabConf, true)) {
            $tabPatternsVerifications[] = array('pattern' => '\p{Lu}', 'message' => 30205);
        }

        if (array_key_multi_get('Key_complexity.Number', $tabConf, true) === true) {
            $tabPatternsVerifications[] = array('pattern' => '[0-9]', 'message' => 30206);
        }

        if (array_key_multi_get('Key_complexity.Special_char', $tabConf, true) === true) {
            $tabPatternsVerifications[] = array('pattern' => '[^\pL\pM\p{Nd}\p{Nl}]', 'message' => 30207);

            return array($tabPatternsVerifications, $tailleMini);
        }

        return array($tabPatternsVerifications, $tailleMini);
    }

    /**
     * @param \DateTime $timeRequete
     * @return bool
     */
    public function hasExpired($timeRequete)
    {
        $tempsValide = 60 * 60 * $this->_timeRequestValid;
        $expires = $timeRequete->getTimestamp() + $tempsValide;
        $nowDateTime = new \DateTime(gmdate('M d Y H:i:s T', time()));

        if ($expires - $nowDateTime->getTimestamp() < 0) {
            return true;
        }

        return false;
    }

    /**
     * @param RequeteManager $requete
     * @return bool
     */
    public function authentifier($requete)
    {
        $couple = explode(':', substr($requete->getAuthorization(), 4));

        if (!is_null($authorization = $this->getUneAuthorization($couple[0]))) {
            $decoderBase64 = base64_decode($couple[1]);

            $motDePasseEncode =
                hash_hmac(
                    'sha256', $requete->getPlainParametres(),
                    $authorization->getClefPrivee() . $requete->getMethode() . $requete->getHttpAccept() .
                        $requete->getDateRequete()->getTimestamp(), true
                );

            return strcmp($decoderBase64, $motDePasseEncode) == 0;
        }

        return false;
    }
}