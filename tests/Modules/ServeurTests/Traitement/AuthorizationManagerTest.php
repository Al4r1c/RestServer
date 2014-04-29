<?php
namespace Tests\ServeurTests\Traitement;

use AlaroxRestServeur\Serveur\Traitement\Authorization\AuthorizationManager;
use Tests\MockArg;
use Tests\TestCase;

class AuthorizationManagerTest extends TestCase
{
    /**
     * @var AuthorizationManager
     */
    private $_authorizationManager;

    static $donneesFactives = array(
        'Activate' => true,
        'Hours_request_valid' => 12,
        'Key_complexity' => array(
            'Min_length' => false,
            'Lower' => false,
            'Upper' => false,
            'Number' => false,
            'Special_char' => false
        ),
        'Authorized' => array()
    );

    public function setUp()
    {
        $this->_authorizationManager = new AuthorizationManager();
    }

    public function testGetAuthorizations()
    {
        $this->_authorizationManager->addAuthorization(
            $auth = $this->getMock('AlaroxRestServeur\Serveur\Traitement\Authorization\Authorization')
        );

        $this->assertCount(1, $this->_authorizationManager->getAuthorizations());
        $this->assertContains($auth, $this->_authorizationManager->getAuthorizations());
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testGetAddAuthorizationsType()
    {
        $this->_authorizationManager->addAuthorization(array());
    }

    public function testTimeActif()
    {
        $this->_authorizationManager->setTimeRequestValid(24);

        $this->assertAttributeEquals(24, '_timeRequestValid', $this->_authorizationManager);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testTimeActifNumeric()
    {
        $this->_authorizationManager->setTimeRequestValid(array());
    }

    public function testChargerFichier()
    {
        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', self::$donneesFactives)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);

        $this->assertTrue($this->_authorizationManager->isAuthActivated());
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testChargerFichierType()
    {
        $this->_authorizationManager->chargerFichierAuthorisations(array());
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30200
     */
    public function testChargerFichierNotExist()
    {
        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', new \Exception())
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30201
     */
    public function testKeyNotFound()
    {
        $data = self::$donneesFactives;
        unset($data['Activate']);

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $data)
        );
        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30201
     */
    public function testKeyNotFound2()
    {
        $data = self::$donneesFactives;
        unset($data['Key_complexity']);

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $data)
        );
        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30202
     */
    public function testKeyMinLengthInvalid()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Min_length'] = 5.5;

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30203
     */
    public function testKeyTooShort()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Min_length'] = 10;
        $dataConf['Authorized']['testminlength'] = 'tropetit';

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30204
     */
    public function testKeyNeedLower()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Lower'] = true;
        $dataConf['Authorized']['testminuscule'] = '+ONLYMÂJ4';

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30205
     */
    public function testKeyNeedUpper()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Upper'] = true;
        $dataConf['Authorized']['testmaj'] = 'ônly5min@';

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30206
     */
    public function testKeyNeedNumber()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Number'] = true;
        $dataConf['Authorized']['testspecialchar'] = '?TheresNôNumber#';

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    /**
     * @expectedException \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30207
     */
    public function testKeyNeedSpecialChar()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Special_char'] = true;
        $dataConf['Authorized']['testspecialchar'] = 'onlyNôrmàlCHAR';

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    public function testKeyOk()
    {
        $dataConf = self::$donneesFactives;
        $dataConf['Key_complexity']['Min_length'] = 8;
        $dataConf['Key_complexity']['Lower'] = true;
        $dataConf['Key_complexity']['Upper'] = true;
        $dataConf['Key_complexity']['Number'] = true;
        $dataConf['Key_complexity']['Special_char'] = true;
        $dataConf['Authorized']['testspecialchar'] = '123itWillWORK!!!';

        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $dataConf)
        );

        $this->_authorizationManager->chargerFichierAuthorisations($fichier);
    }

    public function testAuthentifierOk()
    {
        $user = 'myUserName';
        $clef = 'PRIVATEK3Y==';
        $methode = 'GET';
        $format = 'application/json';
        $data = '{"parameter":"data"}';
        $dateTime = new \DateTime(gmdate('M d Y H:i:s T', time()));

        //var_dump($dateTime->format('M d Y H:i:s T'));


        $pass = base64_encode(hash_hmac('sha256', $data, $clef . $methode . $format . $dateTime->getTimestamp(), true));

        $authorization = $this->createMock(
            'Auth',
            new MockArg('getEntityId', $user),
            new MockArg('getClefPrivee', $clef)
        );

        $this->_authorizationManager->addAuthorization($authorization);

        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', $methode),
            new MockArg('getHttpAccept', $format),
            new MockArg('getPlainParametres', $data),
            new MockArg('getDateRequete', $dateTime),
            new MockArg('getAuthorization', 'ARS ' . $user . ':' . $pass)
        );

        $this->assertTrue($this->_authorizationManager->authentifier($requete));
    }

    public function testAuthentifierTrouveMaisErreur()
    {
        $user = 'myUserName';
        $clef = 'PRIVATEK3Y==';
        $clef2 = 'AN0THER';
        $methode = 'GET';
        $format = 'application/json';
        $data = '{"parameter":"data"}';
        $dateTime = new \DateTime(gmdate('M d Y H:i:s T', 1365000000));


        $pass = base64_encode(hash_hmac('sha256', $data, $clef . $methode . $format . $dateTime->getTimestamp(), true));

        $authorization = $this->createMock(
            'Auth',
            new MockArg('getEntityId', $user),
            new MockArg('getClefPrivee', $clef2)
        );

        $this->_authorizationManager->addAuthorization($authorization);

        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getMethode', $methode),
            new MockArg('getHttpAccept', $format),
            new MockArg('getPlainParametres', $data),
            new MockArg('getDateRequete', $dateTime),
            new MockArg('getAuthorization', 'ARS ' . $user . ':' . $pass)
        );

        $this->assertFalse($this->_authorizationManager->authentifier($requete));
    }

    public function testAuthentifierNonTrouvee()
    {
        $user = 'myUserName';
        $clef = 'PRIVATEK3Y==';
        $user2 = 'itsanotheruser';
        $methode = 'GET';
        $format = 'application/json';
        $data = '{"parameter":"data"}';
        $dateTime = new \DateTime(gmdate('M d Y H:i:s T', 1365000000));


        $pass = base64_encode(hash_hmac('sha256', $data, $clef . $methode . $format . $dateTime->getTimestamp(), true));

        $authorization = $this->createMock(
            'Auth',
            new MockArg('getEntityId', $user2)
        );

        $this->_authorizationManager->addAuthorization($authorization);

        $requete = $this->createMock(
            'RequeteManager',
            new MockArg('getAuthorization', 'ARS ' . $user . ':' . $pass)
        );

        $this->assertFalse($this->_authorizationManager->authentifier($requete));
    }

    public function testhasExpired()
    {
        $this->_authorizationManager->setTimeRequestValid(12);
        $this->assertFalse($this->_authorizationManager->hasExpired(new \DateTime(gmdate('M d Y H:i:s T', time()))));
        $this->assertTrue(
            $this->_authorizationManager->hasExpired(
                new \DateTime(gmdate('M d Y H:i:s T', time() - (60 * 60 * 12) - 1))
            )
        );

        $this->_authorizationManager->setTimeRequestValid(24);

        $this->assertFalse(
            $this->_authorizationManager->hasExpired(
                new \DateTime(gmdate('M d Y H:i:s T', time() - (60 * 60 * 12) - 1))
            )
        );
    }

    public function testHadExpiredNoTimeNoCheck() {
        $this->assertFalse($this->_authorizationManager->hasExpired(new \DateTime(gmdate('M d Y H:i:s T', time()))));
    }
}
