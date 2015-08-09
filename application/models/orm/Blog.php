<?php

namespace Orm;

/**
 * Description of Blog
 *
 * @author Forhad
 */
class Blog extends \Forhad\Orm\Model {

    protected $table = "blogs";

    function owner() {
        return $this->belongsTo('User');
    }

    function comments() {
        return $this->hasMany('Comment');
    }

}
