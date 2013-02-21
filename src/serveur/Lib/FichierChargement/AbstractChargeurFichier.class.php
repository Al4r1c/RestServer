<?php
	namespace Serveur\Lib\FichierChargement;

	abstract class AbstractChargeurFichier {
		abstract public function chargerFichier($locationFichier);
	}