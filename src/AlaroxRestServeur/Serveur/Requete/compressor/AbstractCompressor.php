<?php
namespace AlaroxRestServeur\Serveur\Requete\compressor;

abstract class AbstractCompressor
{
    /**
     * @param string $data
     * @return string
     */
    abstract public function uncompress($data);
}