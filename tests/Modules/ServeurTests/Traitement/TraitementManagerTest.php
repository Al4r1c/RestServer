<?php
    namespace Tests\ServeurTests\Traitement;

    use Tests\TestCase;
    use Tests\MockArg;
    use Serveur\Requete\RequeteManager;
    use Serveur\Traitement\TraitementManager;

    class TraitementManagerTest extends TestCase
    {
        public function testTraiterGet()
        {
            $requete = new RequeteManager();
            $requete->setMethode('GET');
            $requete->setParametres(array('data1' => 'var1'));
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('doGet', $objetReponse, array(1, array('data1' => 'var1')))
            );

            /** @var TraitementManager $traitementManager */
            $traitementManager = $this->createMock(
                'TraitementManager',
                new MockArg('getRessourceClass', $abstractRessource, array('path'))
            );

            $this->assertEquals(
                array('here' => 'some data'),
                $traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterPut()
        {
            $requete = new RequeteManager();
            $requete->setMethode('PUT');
            $requete->setParametres(array('data1' => 'var1'));
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('doPut', $objetReponse, array(1, array('data1' => 'var1')))
            );

            /** @var TraitementManager $traitementManager */
            $traitementManager = $this->createMock(
                'TraitementManager',
                new MockArg('getRessourceClass', $abstractRessource, array('path'))
            );

            $this->assertEquals(
                array('here' => 'some data'),
                $traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterPost()
        {
            $requete = new RequeteManager();
            $requete->setMethode('POST');
            $requete->setParametres(array('data1' => 'var1'));
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('doPost', $objetReponse, array(1, array('data1' => 'var1')))
            );

            /** @var TraitementManager $traitementManager */
            $traitementManager = $this->createMock(
                'TraitementManager',
                new MockArg('getRessourceClass', $abstractRessource, array('path'))
            );

            $this->assertEquals(
                array('here' => 'some data'),
                $traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterDelete()
        {
            $requete = new RequeteManager();
            $requete->setMethode('DELETE');
            $requete->setVariableUri('/path/1');

            $objetReponse = $this->createMock(
                'ObjetReponse',
                new MockArg('getDonneesReponse', array('here' => 'some data'))
            );

            $abstractRessource = $this->createMock(
                'AbstractRessource',
                new MockArg('doDelete', $objetReponse, array(1))
            );

            /** @var TraitementManager $traitementManager */
            $traitementManager = $this->createMock(
                'TraitementManager',
                new MockArg('getRessourceClass', $abstractRessource, array('path'))
            );

            $this->assertEquals(
                array('here' => 'some data'),
                $traitementManager->traiterRequeteEtRecupererResultat($requete)->getDonneesReponse()
            );
        }

        public function testTraiterRessourceInconnue()
        {
            $requete = new RequeteManager();
            $requete->setMethode('GET');
            $requete->setVariableUri('/unknown');

            /** @var TraitementManager $traitementManager */
            $traitementManager = $this->createMock(
                'TraitementManager',
                new MockArg('getRessourceClass', false, array('unknown'))
            );

            $this->assertEquals(404, $traitementManager->traiterRequeteEtRecupererResultat($requete)->getStatusHttp());
        }

        /**
         * @expectedException \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 30000
         */
        public function testTraiterMethodeInconnue()
        {
            $requete = $this->createMock(
                'RequeteManager',
                new MockArg('getMethode', 'FAKE')
            );
            $requete->setVariableUri('/path/1');

            $traitementManager = $this->createMock(
                'TraitementManager',
                new MockArg('getRessourceClass', $this->getMockAbstractRessource())
            );

            $traitementManager->traiterRequeteEtRecupererResultat($requete);
        }
    }