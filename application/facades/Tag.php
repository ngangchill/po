<?php 
use Ahir\Facades\Facade;

class Tag extends Facade {

    /**
     * Get the connector name of main class
     *
     * @return string
     */
    public static function getFacadeAccessor() 
    { 
        return 'Forhad\App\Tags';
    }

}
