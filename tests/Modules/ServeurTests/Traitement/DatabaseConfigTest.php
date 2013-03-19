<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\Data\DatabaseConfig;
use Tests\MockArg;
use Tests\TestCase;

class DatabaseConfigTest extends TestCase
{
    /**
     * @var DatabaseConfig
     */
    private $_databaseInformations;

    public function setUp()
    {
        $this->_databaseInformations = new DatabaseConfig();
    }

    public function testDriver()
    {
        $this->_databaseInformations->setDriver('driver');
        $this->assertEquals('driver', $this->_databaseInformations->getDriver());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testDriverString()
    {
        $this->_databaseInformations->setDriver(10500);
    }

    public function testUsername()
    {
        $this->_databaseInformations->setUsername('nomCompte');
        $this->assertEquals('nomCompte', $this->_databaseInformations->getUsername());
    }

    public function testPassword()
    {
        $this->_databaseInformations->setPassword('mdpmdpmdp');
        $this->assertEquals('mdpmdpmdp', $this->_databaseInformations->getPassword());
    }

    public function testHost()
    {
        $this->_databaseInformations->setHost('myHost');
        $this->assertEquals('myHost', $this->_databaseInformations->getHost());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testHostString()
    {
        $this->_databaseInformations->setHost(null);
    }

    public function testPort()
    {
        $this->_databaseInformations->setPort(20200);
        $this->assertEquals(20200, $this->_databaseInformations->getPort());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testPortNumeric()
    {
        $this->_databaseInformations->setPort('notNumber');
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30100
     */
    public function testPortOutRange()
    {
        $this->_databaseInformations->setPort(-1);
    }

    public function testDatabase()
    {
        $this->_databaseInformations->setDatabase('maBase');
        $this->assertEquals('maBase', $this->_databaseInformations->getDatabase());
    }

    /**
     * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testDatabaseString()
    {
        $this->_databaseInformations->setDatabase(500);
    }

    public function testRecupererInformationFichier()
    {
        $fichierConfigDb = $this->createMock(
            'Fichier', new MockArg('chargerFichier', array('Driver' => 'monDriver',
                'User' => 'nomCompte',
                'Password' => 'mdp',
                'Host' => 'monHost',
                'Port' => 10500,
                'Database' => 'maDb')));

        $this->_databaseInformations->recupererInformationFichier($fichierConfigDb);

        $this->assertEquals('monDriver', $this->_databaseInformations->getDriver());
        $this->assertEquals(10500, $this->_databaseInformations->getPort());
    }

    /**
     * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
     * @expectedExceptionCode 30101
     */
    public function testInformationManquante()
    {
        $fichierConfigDb = $this->createMock(
            'Fichier', new MockArg('chargerFichier', array('wowowowbug')));

        $this->_databaseInformations->recupererInformationFichier($fichierConfigDb);
    }
}
