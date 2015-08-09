<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Orm;

/**
 * Description of Comment
 *
 * @author Forhad
 */
class Comment extends \Forhad\Orm\Model {

    protected $table = "comments";

    function owner() {
        return $this->belongsTo('User');
    }

    function blog() {
        return $this->belongsTo('Blog');
    }

}

