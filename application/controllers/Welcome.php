<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

    public function index() {
        // $this->load->model('orm/user');
        $data = Orm\User::all();
        foreach ($data as $user) {
            echo $user->username;
            echo '<br>';

            //$this->load->view('welcome_message');
        }
    }

    public function tes($pp = null) {
        echo '<pre>';
        var_dump($pp);
        echo $dog;
        echo '</pre>';
    }

}
