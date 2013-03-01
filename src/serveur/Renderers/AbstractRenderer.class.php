<?php
	namespace Serveur\Renderers;

	abstract class AbstractRenderer {
		/**
		 * @param array $donnees
		 * @return string
		 */
		abstract public function render(array $donnees);
	}