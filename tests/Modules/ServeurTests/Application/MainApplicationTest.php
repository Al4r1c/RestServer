<?php
namespace Tests\ServeurTests\Application;

use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\MainApplication;
use Tests\MockArg;
use Tests\TestCase;

class MainApplicationTest extends TestCase
{
    public function testSetHandlers()
    {
        $errorManager = $this->createMock(
            'ErrorManager', new MockArg('setHandlers')
        );

        $conteneur = $this->createMock(
            'Conteneur', new MockArg('getErrorManager', $errorManager)
        );

        $mainApp = new MainApplication($conteneur);
        $mainApp->setHandlers();
    }

    public function testAjouterObserveur()
    {
        $abstractDisplayer = $this->createMock('AbstractDisplayer');

        $errorManager = $this->createMock(
            'ErrorManager', new MockArg('ajouterObserveur')
        );

        $conteneur = $this->createMock(
            'Conteneur', new MockArg('getErrorManager', $errorManager)
        );

        $mainApp = new MainApplication($conteneur);
        $mainApp->ajouterObserveur($abstractDisplayer);

        $this->assertAttributeContains($abstractDisplayer, '_observeurs', $mainApp);
    }

    public function testRun()
    {
        $requete = $this->createMock(
            'RequeteManager', new MockArg('getFormatsDemandes', array('htm'))
        );

        $objetReponse = $this->getMockObjetReponse();

        $traitementManager = $this->createMock(
            'TraitementManager', new MockArg('traiterRequeteEtRecupererResultat', $objetReponse, array($requete))
        );

        $reponse = $this->createMock(
            'ReponseManager', new MockArg('fabriquerReponse', null, array($objetReponse, array('htm'))),
            new MockArg('getContenuReponse', 'variable1 => valeur1')
        );

        $conteneur = $this->createMock(
            'Conteneur', new MockArg('getRequeteManager', $requete),
            new MockArg('getTraitementManager', $traitementManager), new MockArg('getReponseManager', $reponse)
        );

        $mainApp = new MainApplication($conteneur);
        $this->assertEquals('variable1 => valeur1', $mainApp->run());
    }

    public function testRunFailMainException()
    {
        $reponse = $this->getMockRestReponse();

        $conteneur = $this->createMock(
            'Conteneur', new MockArg('getRequeteManager', new MainException(10000, 505)),
            new MockArg('getReponseManager', $reponse)
        );

        $mainApp = new MainApplication($conteneur);
        $mainApp->run();
    }

}