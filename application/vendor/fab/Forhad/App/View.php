<?php

namespace Forhad\App;

use Philo\Blade\Blade;

class View {

    /**
     * View constructer for create blade instance
     *
     * @return null
     */
    public function __construct() {
        $this->engine = new Blade(
                APPPATH . '/views', APPPATH . '/cache'
        );
    }

    /**
     * Render page 
     *
     * @param  string       $file 
     * @param  array        $arguments 
     * @return null
     */
    public function make($file = false) {
        // determine the vie file
        $view = $file ? $file : ci()->router->directory.'.'.ci()->router->fetch_class() . '.' . ci()->router->fetch_method();
        // banchmark 
        ci()->benchmark->mark('code_end');
        ci()->data['elapsed_time'] = ci()->benchmark->elapsed_time('total_execution_time_start', 'code_end');
        echo $this->engine->view()->make($view, ci()->data);
    }

}
