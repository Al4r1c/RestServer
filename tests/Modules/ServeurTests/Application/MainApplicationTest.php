<?php
    namespace Modules\ServeurTests\Application;

    use Serveur\MainApplication;
    use Modules\TestCase;
    use Modules\MockArg;

    class MainApplicationTest extends TestCase
    {
        public function testSetHandlers()
        {
            $errorManager = $this->createMock('ErrorManager',
                new MockArg('setHandlers'));

            $conteneur = $this->createMock('Conteneur',
                new MockArg('getErrorManager', $errorManager));

            $mainApp = new MainApplication($conteneur);
            $mainApp->setHandlers();
        }

        public function testAjouterObserveur()
        {
            $abstractDisplayer = $this->createMock('AbstractDisplayer');

            $errorManager = $this->createMock('ErrorManager',
                new MockArg('ajouterObserveur'));

            $conteneur = $this->createMock('Conteneur',
                new MockArg('getErrorManager', $errorManager));

            $mainApp = new MainApplication($conteneur);
            $mainApp->ajouterObserveur($abstractDisplayer);

            $this->assertAttributeContains($abstractDisplayer, '_observeurs', $mainApp);
        }

        public function testRun()
        {
            $restManager = $this->createMock('RestManager',
                new MockArg('getParametres', array('variable1' => 'valeur1')),
                new MockArg('setVariablesReponse', null, array(200, array('variable1' => 'valeur1'))),
                new MockArg('fabriquerReponse', 'variable1 => valeur1'));

            $conteneur = $this->createMock('Conteneur',
                new MockArg('getRestManager', $restManager));

            $mainApp = new MainApplication($conteneur);
            $this->assertEquals('variable1 => valeur1', $mainApp->run());
        }

        public function testRunFail()
        {
            $restManager = $this->createMock('RestManager',
                new MockArg('getParametres', new \Exception()),
                new MockArg('setVariablesReponse', null, array(500)),
                new MockArg('fabriquerReponse', 'Erreur => Une erreur est survenue'));

            $conteneur = $this->createMock('Conteneur',
                new MockArg('getRestManager', $restManager));

            $mainApp = new MainApplication($conteneur);
            $this->assertEquals('Erreur => Une erreur est survenue', $mainApp->run());
        }

        public function testRunFailMainException()
        {
            $infoHttpCode = \Serveur\Utils\Constante::chargerConfig('httpcode')[505];

            $restManager = $this->createMock('RestManager',
                new MockArg('getParametres', new \Serveur\Exceptions\Exceptions\MainException(10000, 505)),
                new MockArg('setVariablesReponse', null, array(505,
                    array('Code' => 505,
                        'Status' => $infoHttpCode[0],
                        'Message' => $infoHttpCode[1]))),
                new MockArg('fabriquerReponse', 'Erreur => Une erreur est survenue'));

            $conteneur = $this->createMock('Conteneur',
                new MockArg('getRestManager', $restManager));

            $mainApp = new MainApplication($conteneur);
            $this->assertEquals('Erreur => Une erreur est survenue', $mainApp->run());
        }

        public function testEcrireLogAcces()
        {
            $restRequete = $this->getMockRestRequete();
            $restReponse = $this->getMockRestReponse();

            $restManager = $this->createMock('RestManager',
                new MockArg('getParametres', array('variable1' => 'valeur1')),
                new MockArg('setVariablesReponse', null, array(200, array('variable1' => 'valeur1'))),
                new MockArg('fabriquerReponse', 'variable1 => valeur1'),
                new MockArg('getRestRequest', $restRequete),
                new MockArg('getRestResponse', $restReponse));

            $abstractDisplayer = $this->createMock('AbstractDisplayer',
                new MockArg('ecrireMessageAcces', null, array($restRequete, $restReponse)));

            $errorManager = $this->createMock('ErrorManager',
                new MockArg('ajouterObserveur'));

            $conteneur = $this->createMock('Conteneur',
                new MockArg('getRestManager', $restManager),
                new MockArg('getErrorManager', $errorManager));

            $mainApp = new MainApplication($conteneur);
            $mainApp->ajouterObserveur($abstractDisplayer);
            $mainApp->run();
        }
    }