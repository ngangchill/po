<?php namespace Forhad\Route;

/**
 * Description of RouteFacades
 *
 * @author Forhad
 */
class RouteFacades {
    private $loaded_object;
        
        function __construct(RouteObject &$object)
        {
            $this->loaded_object = &$object;
        }
        
        public function where($parameter, $pattern = NULL)
        {
            $this->loaded_object->where($parameter, $pattern);
            
            return $this;
        }
}
