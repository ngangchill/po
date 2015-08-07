<?php

namespace Forhad\App;

use \Michelf\Markdown,
    \Michelf\MarkdownExtra;
use \Forhad\App\File as File;
use \Forhad\App\Dir as Dir;

define('CONTENT_DIR', 'mdcontent/');
define('MDCONTROLLER', 'learn/');
define('BASE_URL', base_url());
define('CONTENT_EXT', '.md');
define('DATE_FORMAT', 'jS M Y');
define('TABE_CLASS', 'table table-hover');


class Md {

    //markdown to html
    public static function parse($file = NULL) {

        $data = preg_replace('#/\*.+?\*/#s', '', self::readFile($file)); // Remove comments and meta
        $data = preg_replace('@\%BASE\%@', BASE_URL, $data); // convert the base url
        //$html = Markdown::defaultTransform($data);
        $html = MarkdownExtra::defaultTransform($data);
        //$html = preg_replace('<table>', '<table class="' . TABE_CLASS . '">', $html);
        //$Extra = new \ParsedownExtra();
        //$html = $Extra->text($data);
        return self::post_process($html);
    }
    // make full link from incomplete link that present in md file
    public static function post_process($content){
        $xml = new \SimpleXMLElement('<?xml version="1.0" standalone="yes"?><div>' . $content . '</div>');
        foreach ($xml->xpath('//a') as $link) {
            $href = $link->attributes()->href;
            if (strpos($href, 'http://') !== 0) {
                 $link['href'] = base_url(MDCONTROLLER.$href);
            }
        }
        $content = $xml->asXML();
        $content = trim(str_replace('<?xml version="1.0" standalone="yes"?>', '', $content));
        // Clean up and style the tables
        $content = str_replace('<table>', '<table class="table table-hover">', $content);
        return $content;
        
    }

    //html to markdown
    public static function htmlToMd($html = Null) {
        if(!$html) $html = "<h3>Quick, to the Batpoles!</h3>";
        $markdown = new \HTML_To_Markdown($html);
        return $markdown;
    }

    //md
    /**
     * Read file contents
     *
     * @param string $file name
     * @return array $contents an array of meta values
     * $disable_ext = TRUE hole alada vabe ext likhar dorkar nai, eta only getPage function er jonno
     */
    public static function readFile($file = NULL, $disable_ext = FALSE) {
        //read the file if exists
        if (file_exists(CONTENT_DIR . $file . CONTENT_EXT)) {
            return file_get_contents(CONTENT_DIR . $file . CONTENT_EXT);
            //$content = file_get_contents(CONTENT_DIR . $file . CONTENT_EXT);
        } else {
            //$content = file_get_contents(CONTENT_DIR . '404' . CONTENT_EXT);
            show_404();
        }
        //return contents
       // return $content;
    }

    /**
     * Parses the file meta from the txt file header
     *
     * @param string $content the raw txt content
     * @return array $headers an array of meta values
     */
    //private function readFilemeta($contents)
    public static function readFilemeta($id) {
        $content = self::readFile($id);
        global $config;

        $headers = array(
            'title' => 'Title',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'author' => 'Author',
            'slug' => 'Slug',
            'date' => 'Date',
            'robots' => 'Robots'
        );

        foreach ($headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]) {
                $headers[$field] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
            } else {
                $headers[$field] = '';
            }
        }

        if ($headers['date'])
            $headers['date_formatted'] = date(DATE_FORMAT, strtotime($headers['date']));

        return $headers;
    }

    /**
     * Get a list of pages
     *
     * @param string $base_url the base URL of the site
     * @param string $order_by order by "alpha" or "date"
     * @param string $order order "asc" or "desc"
     * @return array $sorted_pages an array of pages
     */
    public static function getPages($directory = FALSE, $order_by = 'alpha', $order = 'asc', $excerpt_length = 50) {
        global $config;
        if ($directory) {
            $pages = self::getFiles(CONTENT_DIR . $directory . '/', '.md');
        } else {
            $pages = self::getFiles(CONTENT_DIR, CONTENT_EXT);
        }
        $sorted_pages = array();
        $date_id = 0;
        foreach ($pages as $key => $page) {
            // Skip 404
            if (basename($page) == '404' . CONTENT_EXT) {
                unset($pages[$key]);
                continue;
            }

            /// Get title and format $page
            $page_content = file_get_contents($page);
            $page_meta = self::_readFilemetaForGetPages($page_content);

            //forhad			
            $page_content = Markdown::defaultTransform(preg_replace('#/\*.+?\*/#s', '', $page_content));

            $url = str_replace(CONTENT_DIR, BASE_URL . 'study/category/', $page);
            //$url = str_replace('index'. CONTENT_EXT, '', $url);
            $url = str_replace(CONTENT_EXT, '', $url);
            $data = array(
                'title' => $page_meta['title'],
                'url' => is_dir($url) ? $url . 'index' : $url,
                'author' => $page_meta['author'],
                'date' => $page_meta['date'],
                'date_formatted' => date($config['date_format'], strtotime($page_meta['date'])),
                'content' => $page_content,
                'excerpt' => self::limitWords(strip_tags($page_content), $excerpt_length)
            );
            if ($order_by == 'date') {
                $sorted_pages[$page_meta['date'] . $date_id] = $data;
                $date_id++;
            } else
                $sorted_pages[] = $data;
        }

        if ($order == 'desc')
            krsort($sorted_pages);
        else
            ksort($sorted_pages);

        return $sorted_pages;
    }

    /**
     * Parses the file meta from the txt file header
     *
     * @param string $content the raw txt content
     * @return array $headers an array of meta values
     */
    //private function readFilemeta($contents)
    public static function _readFilemetaForGetPages($content) {
        global $config;

        $headers = array(
            'title' => 'Title',
            'description' => 'Description',
            'author' => 'Author',
            'slug' => 'Slug',
            'date' => 'Date',
            'robots' => 'Robots'
        );

        foreach ($headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]) {
                $headers[$field] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
            } else {
                $headers[$field] = '';
            }
        }

        if ($headers['date'])
            $headers['date_formatted'] = date(DATE_FORMAT, strtotime($headers['date']));

        return $headers;
    }

    /**
     * Helper function to recusively get all files in a directory
     *
     * @param string $directory start directory
     * @param string $ext optional limit to file extensions
     * @return array the matched files
     */
    static function getFiles($directory = 'mdcontent/', $ext = '.md', $only_directory = FALSE) {
        $array_items = array();
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if ($only_directory) {
                        if (is_dir($directory . "/" . $file)) {
                            $array_items = array_merge($array_items, self::getFiles($directory . "/" . $file, $ext));
                        }
                    } else {
                        if (is_dir($directory . "/" . $file)) {
                            $array_items = array_merge($array_items, self::getFiles($directory . "/" . $file, $ext));
                        } else {
                            $file = $directory . "/" . $file;
                            if (!$ext || strstr($file, $ext))
                                $array_items[] = preg_replace("/\/\//si", "/", $file);
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    /**
     * Helper function to limit the words in a string
     *
     * @param string $string the given string
     * @param int $word_limit the number of words to limit to
     * @return string the limited string
     */
    private static function limitWords($string, $word_limit) {
        $words = explode(' ', $string);
        $excerpt = trim(implode(' ', array_splice($words, 0, $word_limit)));
        if (count($words) > $word_limit)
            $excerpt .= '&hellip;';
        return $excerpt;
    }

    /*     * ************************************************************************************* */

    //pico editor
    private function do_delete() {
        $file_url = isset($_POST['file']) && $_POST['file'] ? $_POST['file'] : '';
        $file = basename(strip_tags($file_url));
        if (!$file)
            die('Error: Invalid file');

        $file .= CONTENT_EXT;
        if (file_exists(CONTENT_DIR . $file))
            die(unlink(CONTENT_DIR . $file));
    }

    static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    //check whatever same file exists or not
    public static function duplicate($file) {
        //if file not exists return false
        if (file_exists(CONTENT_DIR . $file . CONTENT_EXT)) {
            return true;
        } else {
            return false;
        }
        //if file exits ,return true
    }

    static function load_plugins() {
        $d = self::getFiles('application/plugins/', '.php');
        if (!empty($d)) {
            //$pg = new stdClass();
            foreach ($d as $plugin) {

                include_once('application/plugins/' . basename($plugin));
                // cleaning up
                $plugin_name = preg_replace("/\\.[^.\\s]{3}$/", '', basename($plugin));
                // make an instance of the plugin if instance does not exists
                if (class_exists($plugin_name)) {
                    $obj[] = new $plugin_name;
                    //$this->plugins[] = $obj;
                }
            }
        }
        //return instances
        return $obj;
    }

    /**
     * Processes any hooks and runs them
     *
     * @param string $hook_id the ID of the hook i.e functn name in the plugin class
     * @param array $args optional arguments
     */
    //use:
    //	$this->run_hooks("before_load_content",array("forhad"));
    public static function run_hooks($hook_id, $args = array()) {
        $ins = self::load_plugins();
        if (!empty($ins)) {
            foreach ($ins as $plugin) {
                if (is_callable(array($plugin, $hook_id))) {
                    $data = call_user_func_array(array($plugin, $hook_id), array($args));
                }
            }
        }
        return $data;
    }
    
    /*
     * Cats // root folders
     */
    public static function cats($dir = CONTENT_DIR){
        if (Dir::exists($dir))     return Dir::scan($dir);
    }
    
    /*
     * create directory
     */
    public static function createDir($dir, $chmod = 0775) {
      
        return Dir::create(CONTENT_DIR.$dir,$chmod);
    }
    
    public static function filesTree($dir, $return_link) {
        $files_array = Dir::getDirectoryTree($dir);
        $data = Dir::treeView($files_array, $return_link);
        return $data;
    }
}
