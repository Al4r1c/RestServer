<?php
    namespace Modules\ServeurTests\Application;

    use Serveur\MainApplication;
    use Modules\TestCase;
    use Modules\MockArg;
    use Serveur\GestionErreurs\Exceptions\MainException;

    class MainApplicationTest extends TestCase
    {
        public function testSetHandlers()
        {
            $errorManager = $this->createMock(
                'ErrorManager',
                new MockArg('setHandlers')
            );

            $conteneur = $this->createMock(
                'Conteneur',
                new MockArg('getErrorManager', $errorManager)
            );

            $mainApp = new MainApplication($conteneur);
            $mainApp->setHandlers();
        }

        public function testAjouterObserveur()
        {
            $abstractDisplayer = $this->createMock('AbstractDisplayer');

            $errorManager = $this->createMock(
                'ErrorManager',
                new MockArg('ajouterObserveur')
            );

            $conteneur = $this->createMock(
                'Conteneur',
                new MockArg('getErrorManager', $errorManager)
            );

            $mainApp = new MainApplication($conteneur);
            $mainApp->ajouterObserveur($abstractDisplayer);

            $this->assertAttributeContains($abstractDisplayer, '_observeurs', $mainApp);
        }

        public function testRun()
        {
            $requete = $this->createMock(
                'RequeteManager',
                new MockArg('getParametres', array('variable1' => 'valeur1'))
            );

            $reponse = $this->createMock(
                'ReponseManager',
                new MockArg('setStatus', null, array(200)),
                new MockArg('setContenu', null, array(array('variable1' => 'valeur1'))),
                new MockArg('fabriquerReponse', 'variable1 => valeur1')
            );

            $conteneur = $this->createMock(
                'Conteneur',
                new MockArg('getRequeteManager', $requete),
                new MockArg('getReponseManager', $reponse)
            );

            $mainApp = new MainApplication($conteneur);
            $this->assertEquals('variable1 => valeur1', $mainApp->run());
        }

        public function testRunFailMainException()
        {
            $infoHttpCode = \Serveur\Utils\Constante::chargerConfig('httpcode')[505];

            $requete = $this->createMock(
                'RequeteManager',
                new MockArg('getParametres', new MainException(10000, 505))
            );

            $reponse = $this->createMock(
                'ReponseManager',
                new MockArg('setStatus', null, array(505)),
                new MockArg('setContenu', null, array(array('Code' => 505,
                    'Status' => $infoHttpCode[0],
                    'Message' => $infoHttpCode[1]))),
                new MockArg('fabriquerReponse', 'Erreur => Une erreur est survenue')
            );

            $conteneur = $this->createMock(
                'Conteneur',
                new MockArg('getRequeteManager', $requete),
                new MockArg('getReponseManager', $reponse)
            );

            $mainApp = new MainApplication($conteneur);
            $this->assertEquals('Erreur => Une erreur est survenue', $mainApp->run());
        }

        public function testRunFailExceptionGenerique()
        {
            $requete = $this->createMock(
                'RequeteManager',
                new MockArg('getParametres', new \Exception())
            );

            $reponse = $this->createMock(
                'ReponseManager',
                new MockArg('setStatus', null, array(500)),
                new MockArg('fabriquerReponse', 'Erreur => Une erreur est survenue')
            );

            $conteneur = $this->createMock(
                'Conteneur',
                new MockArg('getRequeteManager', $requete),
                new MockArg('getReponseManager', $reponse)
            );

            $mainApp = new MainApplication($conteneur);
            $this->assertEquals('Erreur => Une erreur est survenue', $mainApp->run());
        }

        public function testEcrireLogAcces()
        {
            $requete = $this->createMock(
                'RequeteManager',
                new MockArg('getParametres', array('variable1' => 'valeur1'))
            );

            $reponse = $this->createMock(
                'ReponseManager',
                new MockArg('setStatus', null, array(200)),
                new MockArg('setContenu', null, array(array('variable1' => 'valeur1'))),
                new MockArg('fabriquerReponse', 'variable1 => valeur1')
            );

            $abstractDisplayer = $this->createMock(
                'AbstractDisplayer',
                new MockArg('logRequete', null, array($requete)),
                new MockArg('logReponse', null, array($reponse))
            );

            $errorManager = $this->createMock(
                'ErrorManager',
                new MockArg('ajouterObserveur')
            );

            $conteneur = $this->createMock(
                'Conteneur',
                new MockArg('getRequeteManager', $requete),
                new MockArg('getReponseManager', $reponse),
                new MockArg('getErrorManager', $errorManager)
            );

            $mainApp = new MainApplication($conteneur);
            $mainApp->ajouterObserveur($abstractDisplayer);
            $mainApp->run();
        }
    }