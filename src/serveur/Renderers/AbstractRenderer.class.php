<?php
	namespace Serveur\Renderers;

	abstract class AbstractRenderer {
		abstract public function render(array $donnees);
	}