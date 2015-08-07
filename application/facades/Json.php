<?php 

use Ahir\Facades\Facade;

class Json extends Facade {

    /**
     * Get the connector name of main class
     *
     * @return string
     */
    public static function getFacadeAccessor() 
    { 
        return 'Forhad\App\JsonHandler';
    }

}