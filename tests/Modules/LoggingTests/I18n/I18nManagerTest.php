<?php
namespace Tests\LoggingTests\I18n;

use Logging\I18n\I18nManager;
use Tests\MockArg;
use Tests\TestCase;

class I18nManagerTest extends TestCase
{

    public function testSetLangueDefaut()
    {
        $i18nManager = new I18nManager();
        $i18nManager->setLangueDefaut('Mexicain');

        $this->assertAttributeEquals('Mexicain', '_langueDefaut', $i18nManager);
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage Default language is not set properly.
     */
    public function testSetLangueDefautErreur()
    {
        $i18nManager = new I18nManager();
        $i18nManager->setLangueDefaut(null);
    }

    public function testSetLangueDispo()
    {
        $i18nManager = new I18nManager();
        $i18nManager->setLangueDispo(array('Allemand' => 'al', 'Kosovar' => 'ksv'));

        $this->assertAttributeEquals(
            array('Allemand' => 'al', 'Kosovar' => 'ksv'), '_languesDisponibles', $i18nManager
        );
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage No available language set.
     */
    public function testSetLangueDispoErreur()
    {
        $i18nManager = new I18nManager();
        $i18nManager->setLangueDispo(array());
    }

    public function testSetConfig()
    {
        $i18nManager = new I18nManager();
        $i18nManager->setConfig('French', array('French' => 'fr', 'English' => 'en'));

        $this->assertAttributeEquals('French', '_langueDefaut', $i18nManager);

        $this->assertAttributeEquals(
            array('French' => 'fr', 'English' => 'en'), '_languesDisponibles', $i18nManager
        );
    }

    public function testGetFichierTraduction()
    {
        $xmlParser = $this->createMock(
            'xmlparser', new MockArg('isValidXML', true)
        );

        $fichier = $this->createMock(
            'Fichier', new MockArg('fileExist', true), new MockArg('loadFile', $xmlParser)
        );

        /** @var $i18nManager I18nManager */
        $i18nManager = $this->createMock(
            'I18nManager', new MockArg('getFichier', $fichier, array('fr'))
        );

        $i18nManager->setConfig('French', array('FRENCH' => 'fr', 'ENGLISH' => 'en'));

        $this->assertThat(
            $i18nManager->getFichierTraduction(), $this->logicalAnd(
                $this->logicalNot($this->isNull()), $this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
            )
        );
    }

    public function testGetFichierTraductionDefautInexistant()
    {
        $xmlParser = $this->createMock(
            'xmlparser', new MockArg('isValidXML', true)
        );

        $fichier = $this->createMock(
            'Fichier', new MockArg('fileExist', true), new MockArg('loadFile', $xmlParser)
        );

        /** @var $i18nManager I18nManager */
        $i18nManager = $this->createMock(
            'I18nManager', new MockArg('getFichier', $fichier, array('en'))
        );

        $i18nManager->setConfig('French', array('ENGLISH' => 'en'));

        $this->assertThat(
            $i18nManager->getFichierTraduction(), $this->logicalAnd(
                $this->logicalNot($this->isNull()), $this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
            )
        );
    }

    public function testGetFichierTraductionDefautInvalide()
    {
        $xmlParserCn = $this->createMock(
            'xmlparser', new MockArg('isValidXML', true)
        );

        $xmlParserFr = $this->createMock(
            'xmlparser', new MockArg('isValidXML', false)
        );

        $fichierCn = $this->createMock(
            'Fichier', new MockArg('fileExist', true), new MockArg('loadFile', $xmlParserCn)
        );

        $fichierFr = $this->createMock(
            'Fichier', new MockArg('fileExist', true), new MockArg('loadFile', $xmlParserFr)
        );

        /** @var $i18nManager I18nManager */
        $i18nManager = $this->createMock(
            'I18nManager', new MockArg('getFichier', $fichierFr, array('fr')),
            new MockArg('getFichier', $fichierCn, array('cn'))
        );

        $i18nManager->setConfig('French', array('CHINOIS' => 'cn', 'FRENCH' => 'fr'));

        $this->assertThat(
            $i18nManager->getFichierTraduction(), $this->logicalAnd(
                $this->logicalNot($this->isNull()), $this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
            )
        );
    }

    public function testGetFichierTraductionPlusieursFichierInexistantOuBugges()
    {
        $xmlParserIt = $this->createMock(
            'xmlparser', new MockArg('isValidXML', false)
        );

        $xmlParserFr = $this->createMock(
            'xmlparser', new MockArg('isValidXML', true)
        );

        $fichierEn = $this->createMock(
            'Fichier', new MockArg('fileExist', false)
        );

        $fichierIt = $this->createMock(
            'Fichier', new MockArg('fileExist', true), new MockArg('loadFile', $xmlParserIt)
        );

        $fichierFr = $this->createMock(
            'Fichier', new MockArg('fileExist', true), new MockArg('loadFile', $xmlParserFr)
        );

        /** @var $i18nManager I18nManager */
        $i18nManager = $this->createMock(
            'I18nManager', new MockArg('getFichier', $fichierEn, array('en')),
            new MockArg('getFichier', $fichierEn, array('en')), new MockArg('getFichier', $fichierIt, array('it')),
            new MockArg('getFichier', $fichierFr, array('fr'))
        );

        $i18nManager->setConfig('English', array('ENGLISH' => 'en', 'ITALIAN' => 'it', 'FRENCH' => 'fr'));

        $this->assertThat(
            $i18nManager->getFichierTraduction(), $this->logicalAnd(
                $this->logicalNot($this->isNull()), $this->isInstanceOf('Serveur\Lib\XMLParser\XMLParser')
            )
        );
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionMessage No valid translation file set or found.
     */
    public function testGetFichierTraductionAucunFichierFonctionnel()
    {
        $fichier = $this->createMock(
            'Fichier', new MockArg('fileExist', false)
        );

        /** @var $i18nManager I18nManager */
        $i18nManager = $this->createMock(
            'I18nManager', new MockArg('getFichier', $fichier)
        );

        $i18nManager->setConfig('English', array('ENGLISH' => 'en'));

        $i18nManager->getFichierTraduction();
    }
}