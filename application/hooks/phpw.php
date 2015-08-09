<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require(__DIR__ . '/../vendor/autoload.php'); 

//use whoops for error debugging
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;


class PHPW {
    static function reload() {
		$run     = new Whoops\Run;
		$handler = new PrettyPageHandler;
		$CI = &get_instance();
		$handler->setEditor("sublime");         // Set the editor used for the "Open" link
		$handler->addDataTable("Extra Info", array(
				"Name" => 'Forhad Ahmed',
				"email"   => "forhad.ahmed@outlook.com",
				//"route" => $CI->uri->uri_string()
			));
		// Set the title of the error page:
		$handler->setPageTitle("Whoops! There was a problem.");
		$run->pushHandler($handler);
		$CI = &load_class('Input');
		
		if ($CI->is_ajax_request() == true) {
			$run->pushHandler(new JsonResponseHandler);
		}
		
		// Register the handler with PHP, and you're set!
		$run->register();
    }
}