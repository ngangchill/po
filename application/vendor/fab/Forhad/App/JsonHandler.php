<?php
namespace Forhad\App;

/**
 * Description of JsonHandler
 *
 * @author Forhad
 */
class JsonHandler {
    protected static $_message = array(
        JSON_ERROR_NONE => 'No error occure',
        JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'invalid or malformed json',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly enclosed',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed utf8 character, possibly incorrectly enclosed',
    );
    
    public static function encode($value, $options = 0) {
        $result = json_encode($value, $options);
        if($result){
            return $result;
        }
        throw new \RuntimeException(static::$_message[json_last_error()]);
    }
    
    public static function decode($value, $assoc = false) {
        $result = json_decode($value, $assoc);
        if($result){
            return $result;
        }
        throw new \RuntimeException(static::$_message[json_last_error()]);
    }
}
