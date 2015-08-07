<?php namespace Forhad\Dummy;

/**
 * Description of Dumy
 *
 * @author Forhad
 */
class Dumy {
    //object
    //public $article;
    
    public function __construct() {
        //return $this->string($params);
    }
    
    public function string($params) {
        $data = new \stdClass();
        
        foreach ($params as $value) {
            $data->$value = '';
        }
        return $data;
    }
}
