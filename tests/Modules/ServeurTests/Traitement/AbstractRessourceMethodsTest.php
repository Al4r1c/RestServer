<?php
namespace Tests\ServeurTests\Traitement;

use AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager;
use AlaroxRestServeur\Serveur\Traitement\Ressource\AbstractRessource;
use Tests\MockArg;
use Tests\TestCase;

class AbstractRessourceMethodsTest extends TestCase
{
    /**
     * @var AbstractRessource
     */
    private $_abstractRess;

    public function setUp()
    {
        $this->_abstractRess = null;
    }

    /**
     * @param MockArg $mockArg
     */
    public function setFakeDatabase($mockArg)
    {
        $abstractDatabase = $this->createMock('AbstractDatabase', $mockArg);

        $this->_abstractRess = $this->createMock('AbstractRessource');

        $this->_abstractRess->setConnectionDatabase($abstractDatabase);
    }

    /**
     * @return ParametresManager
     */
    public function getFakeParamManager()
    {
        return $this->createMock('ParametresManager');
    }

    public function testGetOne()
    {
        $this->setFakeDatabase(new MockArg('recupererId', array('hello world'), array(5, false)));

        $this->assertEquals(array('hello world'), $this->_abstractRess->getOne(5, false));
    }

    public function testGetAll()
    {
        $paramManager = $this->getFakeParamManager();

        $this->setFakeDatabase(new MockArg('recuperer', array('hello world'), array($paramManager)));

        $this->assertEquals(array('hello world'), $this->_abstractRess->getAll($paramManager));
    }

    public function testCreateOne()
    {
        $paramManager = $this->getFakeParamManager();

        $this->setFakeDatabase(new MockArg('inserer', array('hello world'), array($paramManager)));

        $this->assertEquals(array('hello world'), $this->_abstractRess->createOne($paramManager));
    }

    public function testUpdateOne()
    {
        $paramManager = $this->getFakeParamManager();

        $this->setFakeDatabase(new MockArg('mettreAJour', array('hello world'), array(50, $paramManager)));

        $this->assertEquals(array('hello world'), $this->_abstractRess->updateOne(50, $paramManager));
    }

    public function testCreateUpdateIdempotent()
    {
        $paramManager = $this->getFakeParamManager();

        $this->setFakeDatabase(new MockArg('insererIdempotent', array('hello world'), array(50, $paramManager)));

        $this->assertEquals(array('hello world'), $this->_abstractRess->createOrUpdateIdempotent(50, $paramManager));
    }

    public function testDeleteOne()
    {
        $this->setFakeDatabase(new MockArg('supprimerId', array('hello world'), array(50)));

        $this->assertEquals(array('hello world'), $this->_abstractRess->deleteOne(50));
    }

    public function testDeleteAll()
    {
        $this->setFakeDatabase(new MockArg('supprimer', array('hello world')));

        $this->assertEquals(array('hello world'), $this->_abstractRess->deleteAll());
    }

    public function testPutCollection()
    {
        $paramManager = $this->getFakeParamManager();

        $this->setFakeDatabase(
            new MockArg('setCollection', array('hello world'), array(50, 'collectionName', $paramManager))
        );

        $this->assertEquals(
            array('hello world'),
            $this->_abstractRess->putCollection(50, 'collectionName', $paramManager)
        );
    }

    public function testPutOneInCollection()
    {
        $paramManager = $this->getFakeParamManager();

        $this->setFakeDatabase(
            new MockArg('ajouterDansCollection', array('hello world'), array(50, 'collectionName', $paramManager))
        );

        $this->assertEquals(
            array('hello world'),
            $this->_abstractRess->putOneInCollection(50, 'collectionName', $paramManager)
        );
    }

    public function testDeleteCollection()
    {
        $this->setFakeDatabase(new MockArg('supprimerCollection', array('hello world'), array(50, 'collectionName')));

        $this->assertEquals(array('hello world'), $this->_abstractRess->deleteCollection(50, 'collectionName'));
    }

    public function testDeleteInCollection()
    {
        $this->setFakeDatabase(
            new MockArg('supprimerDansCollection', array('hello world'), array(50, 'collectionName', 5))
        );

        $this->assertEquals(array('hello world'), $this->_abstractRess->deleteInCollection(50, 'collectionName', 5));
    }
}