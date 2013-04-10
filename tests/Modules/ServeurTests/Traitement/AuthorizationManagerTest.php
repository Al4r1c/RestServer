<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\Authorization\AuthorizationManager;
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
            $auth = $this->getMock('Serveur\Traitement\Authorization\Authorization')
        );

        $this->assertCount(1, $this->_authorizationManager->getAuthorizations());
        $this->assertContains($auth, $this->_authorizationManager->getAuthorizations());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testGetAddAuthorizationsType()
    {
        $this->_authorizationManager->addAuthorization(array());
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function testChargerFichierType()
    {
        $this->_authorizationManager->chargerFichierAuthorisations(array());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
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
}
