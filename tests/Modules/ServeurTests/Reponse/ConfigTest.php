<?php
namespace Tests\ServeurTests\Reponse;

use AlaroxFileManager\FileManager\File;
use AlaroxRestServeur\Serveur\Reponse\Config\Config;
use Tests\MockArg;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    /** @var Config */
    private $configuration;
    private static $donneesConfig = array('Config' => array('DEBUG_WEBSITE' => true,
        'DEBUG_FRAMEWORK' => true,
        'CHARSET' => 'utf-8',
        'DEFAULT_RENDER' => 'XML'),
        'Render' => array('XML' => 'xml'));

    public function setUp()
    {
        $this->configuration = new Config();
    }

    public function testChargerFichier()
    {
        /** @var $fichier File */
        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', self::$donneesConfig)
        );

        $this->configuration->chargerConfiguration($fichier);

        $this->assertAttributeEquals(
            array_change_key_case(self::$donneesConfig, CASE_UPPER), '_applicationConfiguration', $this->configuration
        );
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testChargerConfigurationArgumentDoitEtreFichier()
    {
        $this->configuration->chargerConfiguration(null);
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40200
     */
    public function testloadFileInexistant()
    {
        $fichier = $this->createMock(
            'Fichier', new MockArg('fileExist', false)
        );

        $this->configuration->chargerConfiguration($fichier);
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 40201
     */
    public function testChargerFichierInvalide()
    {
        $donnees = self::$donneesConfig;
        unset($donnees['Render']);
        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', $donnees)
        );

        $this->configuration->chargerConfiguration($fichier);
    }

    public function testGetValeur()
    {
        $fichier = $this->createMock(
            'Fichier', new MockArg('loadFile', self::$donneesConfig)
        );

        $this->configuration->chargerConfiguration($fichier);

        $this->assertEquals('utf-8', $this->configuration->getConfigValeur('config.charset'));
        $this->assertEquals('xml', $this->configuration->getConfigValeur('render.xml'));
        $this->assertNull($this->configuration->getConfigValeur('render.existepas'));
    }

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testGetValeurNonString()
    {
        $this->configuration->getConfigValeur(3);
    }
}