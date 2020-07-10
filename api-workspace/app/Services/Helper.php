<?php

namespace App\Services;
 

class Helper 
{
    /**
     * Method split string by a string
     * @param string $separator     Specifies string where to break the string
     * @param string $string        The string to split
     * @return array
     */
    public static function explode($separator, $string){
        return strpos($string, $separator) !== false ? explode($separator, $string) : (isset($string) && !empty($string) ? array($string): array());
    }
}
