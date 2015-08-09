<?php

class MY_Controller extends CI_Controller {

    /**
     * The type of caching to use. The default values are
     * set here so they can be used everywhere, but
     */
    protected $cache_type = 'dummy';
    protected $backup_cache = 'file';
    // If TRUE, will send back the notices view
    // through the 'render_json' method in the
    // 'fragments' array.
    protected $ajax_notices = true;
    private $use_view = '';
    private $use_layout = '';
    // Stores data variables to be sent to the view.
    protected $data = array();
    // For status messages
    protected $message;
    // Should we try to migrate to the latest version
    // on every page load?
    protected $controller_name;
    protected $action_name;
    protected $previous_controller_name;
    protected $previous_action_name;
    protected $save_previous_url = false;
    protected $page_title;
    protected $error = array();
    private $__filter_params;

    //--------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $this->__filter_params = array($this->uri->uri_string());

        $this->call_filters('before');
        //--------------------------------------------------------------------
        // Cache Setup
        //--------------------------------------------------------------------
        // Make sure that caching is ALWAYS available throughout the app
        $this->load->driver('cache', array('adapter' => $this->cache_type, 'backup' => $this->backup_cache));

        //--------------------------------------------------------------------
        // Profiler
        //--------------------------------------------------------------------
        if ($this->config->item('show_profiler') == true) {
            $this->output->enable_profiler(true);
        }

//         //save the previous controller and action name from session
//        $this->previous_controller_name = $this->session->flashdata('previous_controller_name'); 
//        $this->previous_action_name     = $this->session->flashdata('previous_action_name'); 
//        
//        //set the current controller and action name
//        $this->controller_name = $this->router->fetch_directory() . $this->router->fetch_class();
//        $this->action_name     = $this->router->fetch_method();
    }

    public function _remap($method, $parameters = array()) {

        empty($parameters) ? $this->$method() : call_user_func_array(array($this, $method), $parameters);

        if ($method != 'call_filters') {
            $this->call_filters('after');
        }
    }

    private function call_filters($type) {

        $loaded_route = $this->router->get_active_route();
        $filter_list = Route::get_filters($loaded_route, $type);

        foreach ($filter_list as $filter_data) {
            $param_list = $this->__filter_params;

            $callback = $filter_data['filter'];
            $params = $filter_data['parameters'];

            // check if callback has parameters
            if (!is_null($params)) {
                // separate the multiple parameters in case there are defined
                $params = explode(':', $params);

                // search for uris defined as parameters, they will be marked as {(.*)}
                foreach ($params as &$p) {
                    if (preg_match('/\{(.*)\}/', $p, $match_p)) {
                        $p = $this->uri->segment($match_p[1]);
                    }
                }

                $param_list = array_merge($param_list, $params);
            }

            if (class_exists('Closure') and method_exists('Closure', 'bind')) {
                $callback = Closure::bind($callback, $this);
            }

            call_user_func_array($callback, $param_list);
        }
    }

}

class Installer extends MY_Controller {

    protected $auto_migrate = false;

    public function __construct() {
        parent::__construct();
        //--------------------------------------------------------------------
        // Migrations
        //--------------------------------------------------------------------
        // Try to auto-migrate any files stored in APPPATH ./migrations
        if ($this->auto_migrate === TRUE) {
            $this->load->library('migration');
            // We can specify a version to migrate to by appending ?migrate_to=X
            // in the URL.
            if ($mig_version = $this->input->get('migrate_to')) {
                $this->migration->version($mig_version);
            } else {
                $this->migration->latest();
            }
        }
    }

}
