<?php

namespace Forhad\App;

/**
 * Description of Htag System
 *
 * @author Forhad
 */
class Htag {

    const version = '0.0.1';
    
    protected static $url = 'hashtag.php';

    public static function make($str) {
        $regex = "/#+([a-zA-Z_]+)/";
        $str = preg_replace($regex, '<a href="'.self::$url.'?tag=$1">$0</a>', $str);
        return($str);
    }

}
