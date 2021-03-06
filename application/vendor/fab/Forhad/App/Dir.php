<?php

namespace Forhad\App;

/**
 * Gelato Library
 *
 * This source file is part of the Gelato Library. More information,
 * documentation and tutorials can be found at http://gelato.monstra.org
 *
 * @package     Gelato
 *
 * @author      Romanenko Sergey / Awilum <awilum@msn.com>
 * @copyright   2012-2014 Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Dir {

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct() {
        // Nothing here
    }

    /**
     * Creates a directory
     *
     *  <code>
     *      Dir::create('folder1');
     *  </code>
     *
     * @param  string  $dir   Name of directory to create
     * @param  integer $chmod Chmod
     * @return boolean
     */
    public static function create($dir, $chmod = 0775) {
        // Redefine vars
        $dir = (string) $dir;

        // Create new dir if $dir !exists
        return (!Dir::exists($dir)) ? @mkdir($dir, $chmod, true) : true;
    }

    /**
     * Checks if this directory exists.
     *
     *  <code>
     *      if (Dir::exists('folder1')) {
     *          // Do something...
     *      }
     *  </code>
     *
     * @param  string  $dir Full path of the directory to check.
     * @return boolean
     */
    public static function exists($dir) {
        // Redefine vars
        $dir = (string) $dir;

        // Directory exists
        if (file_exists($dir) && is_dir($dir))
            return true;

        // Doesn't exist
        return false;
    }

    /**
     * Check dir permission
     *
     *  <code>
     *      echo Dir::checkPerm('folder1');
     *  </code>
     *
     * @param  string $dir Directory to check
     * @return string
     */
    public static function checkPerm($dir) {
        // Redefine vars
        $dir = (string) $dir;

        // Clear stat cache
        clearstatcache();

        // Return perm
        return substr(sprintf('%o', fileperms($dir)), -4);
    }

    /**
     * Delete directory
     *
     *  <code>
     *      Dir::delete('folder1');
     *  </code>
     *
     * @param string $dir Name of directory to delete
     */
    public static function delete($dir) {
        // Redefine vars
        $dir = (string) $dir;

        // Delete dir
        if (is_dir($dir)) {
            $ob = scandir($dir);
            foreach ($ob as $o) {
                if ($o != '.' && $o != '..') {
                    if (filetype($dir . '/' . $o) == 'dir')
                        Dir::delete($dir . '/' . $o);
                    else
                        unlink($dir . '/' . $o);
                }
            }
        }
        reset($ob);
        rmdir($dir);
    }

    /**
     * Get list of directories
     *
     *  <code>
     *      $dirs = Dir::scan('folders');
     *  </code>
     *
     * @param string $dir Directory
     */
    public static function scan($dir) {
        // Redefine vars
        $dir = (string) $dir;

        // Scan dir
        if (is_dir($dir) && $dh = opendir($dir)) {
            $f = array();
            while ($fn = readdir($dh)) {
                if ($fn != '.' && $fn != '..' && is_dir($dir . DIRECTORY_SEPARATOR . $fn))
                    $f[] = $fn;
            }return$f;
        }
    }

    /**
     * Check if a directory is writable.
     *
     *  <code>
     *      if (Dir::writable('folder1')) {
     *          // Do something...
     *      }
     *  </code>
     *
     * @param  string   $path The path to check.
     * @return booleans
     */
    public static function writable($path) {
        // Redefine vars
        $path = (string) $path;

        // Create temporary file
        $file = tempnam($path, 'writable');

        // File has been created
        if ($file !== false) {

            // Remove temporary file
            File::delete($file);

            //  Writable
            return true;
        }

        // Else not writable
        return false;
    }

    /**
     * Get directory size.
     *
     *  <code>
     *      echo Dir::size('folder1');
     *  </code>
     *
     * @param  string  $path The path to directory.
     * @return integer
     */
    public static function size($path) {
        // Redefine vars
        $path = (string) $path;

        $total_size = 0;
        $files = scandir($path);
        $clean_path = rtrim($path, '/') . '/';

        foreach ($files as $t) {
            if ($t <> "." && $t <> "..") {
                $current_file = $clean_path . $t;
                if (is_dir($current_file)) {
                    $total_size += Dir::size($current_file);
                } else {
                    $total_size += filesize($current_file);
                }
            }
        }

        // Return total size
        return $total_size;
    }

    /**
     * Copy directory.
     * <code>
     *      Dir::copy('source_folder_path', 'destination_folder_path);
     *  </code>
     * @param $src
     * @param $dst
     */
    public static function copy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    self::copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    /*
     * param  directory name
     * return array
     */
    public static function getDirectoryTree($outerDir) {
        $dirs = array_diff(scandir($outerDir), Array(".", "..", "404.md", "index.html",".htaccess"));
        $dir_array = Array();
        foreach ($dirs as $d) {
            if (is_dir($outerDir . "/" . $d))
                $dir_array[$d] = self::getDirectoryTree($outerDir . "/" . $d);
            else
                //$dir_array[$d] = $d;
                $dir_array[$d] = basename($d, '.md');
        }
        return $dir_array;
    }
    /*
     * params array,
     * params url
     * return string
     */
    public static function treeView($array, $return_link) {
        $tree = '<ul class="list-unstyled list-spaces">';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $tree .= '<li><a href="' . $return_link . '/' . $key . '"><i class="fa fa-arrow-circle-o-right fa-2"></i> <strong>' . ucfirst(str_replace('_', ' ', $key)) . '</strong></li>';
                $tree .= '<ul>';
                $tree .= self::treeView($value, $return_link . '/' . $key);
                $tree .= '</ul>';
            } else {
                $tree .= '<li><a href="' . $return_link . '/' . $value . '"><i class="fa fa-dot-circle-o fa-2"></i> ' . ucfirst(str_replace('_', ' ', $value)) . '</li>';
            }
        }
        $tree .= '</ul>';
        return $tree;
    }

}
