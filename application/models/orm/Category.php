<?php namespace Orm;

/**
 * Description of Category
 *
 * @author Forhad
 */
class Category extends \Forhad\Orm\Model{
    protected $table = "categories";
    
    function blogs() {
        return $this->hasMany('Blog');
    }
}
