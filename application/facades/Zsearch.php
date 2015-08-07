<?php
use Ahir\Facades\Facade;
/**
 * Description of Zsearch
 *
 * @author Forhad
 */
class Zsearch extends Facade {

    /**
     * Get the connector name of main class
     *
     * @return string
     */
    public static function getFacadeAccessor() 
    { 
        return 'Forhad\Search\Fsearch';
    }

}