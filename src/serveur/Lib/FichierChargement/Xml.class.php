<?php
    namespace Serveur\Lib\FichierChargement;

    use Serveur\Lib\XMLParser\XMLParser;

    class Xml extends AbstractChargeurFichier
    {
        /**
         * @param string $locationFichier
         * @return \Serveur\Lib\XMLParser\XMLParser
         */
        public function chargerFichier($locationFichier)
        {
            $donneesXml = file_get_contents($locationFichier);

            $xmlParsee = new XMLParser();
            $xmlParsee->setContenuInitial($donneesXml);
            $xmlParsee->parse();

            return $xmlParsee;
        }
    }