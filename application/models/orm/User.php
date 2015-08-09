<?php

namespace Orm;

/**
 * Description of User
 *
 * @author Forhad
 */
class User extends \Forhad\Orm\Model {

    protected $table = "users";

    function blog() {
        return $this->hasOne('Blog');
    }

    function comments() {
        return $this->hasMany('Comment');
    }

}
