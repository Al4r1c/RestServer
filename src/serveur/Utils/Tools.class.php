<?php
namespace Serveur\Utils;

class Tools
{
    /**
     * @param int $codeHttp
     * @return bool
     */
    public static function isValideHttpCode($codeHttp)
    {
        return array_key_exists($codeHttp, Constante::chargerConfig('httpcode'));
    }

    /**
     * @param string $header
     * @return bool
     */
    public static function isValideHeader($header)
    {
        return in_array(strtolower($header), array_map('strtolower', Constante::chargerConfig('headers')));
    }
}