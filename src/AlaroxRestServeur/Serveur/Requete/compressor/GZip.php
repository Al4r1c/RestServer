<?php
namespace AlaroxRestServeur\Serveur\Requete\compressor;

class GZip extends AbstractCompressor
{
    /**
     * @param string $data
     * @return string
     */
    public function uncompress($data)
    {
        return gzdecode($data);
    }
}