<?php 

use Ahir\Facades\Facade;

class DB extends Facade {

    /**
     * Get the connector name of main class
     *
     * @return string
     */
    public static function getFacadeAccessor() 
    { 
        return 'Illuminate\Database\Capsule\Manager';
    }

}