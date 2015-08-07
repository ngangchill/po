<?php
namespace Forhad\App;
/*Usage:
scanDir::scan(path(s):string|array, [file_extensions:string|array], [subfolders?:true|false]);
*/
class ScanDir {
    static private $directories, $files, $ext_filter, $recursive;
	//public $home_dir = $directories;
	
// ----------------------------------------------------------------------------------------------
    // scan(dirpath::string|array, extensions::string|array, recursive::true|false)
    static public function scan(){
        // Initialize defaults
        self::$recursive = false;
        self::$directories = array();
        self::$files = array();
        self::$ext_filter = false;

        // Check we have minimum parameters
        if(!$args = func_get_args()){
            die("Must provide a path string or array of path strings");
        }
        if(gettype($args[0]) != "string" && gettype($args[0]) != "array"){
            die("Must provide a path string or array of path strings");
        }

        // Check if recursive scan | default action: no sub-directories
        if(isset($args[2]) && $args[2] == true){self::$recursive = true;}

        // Was a filter on file extensions included? | default action: return all file types
        if(isset($args[1])){
            if(gettype($args[1]) == "array"){self::$ext_filter = array_map('strtolower', $args[1]);}
            else
            if(gettype($args[1]) == "string"){self::$ext_filter[] = strtolower($args[1]);}
        }

        // Grab path(s)
        self::verifyPaths($args[0]);
        return self::$files;
    }

    static private function verifyPaths($paths){
        $path_errors = array();
        if(gettype($paths) == "string"){$paths = array($paths);}

        foreach($paths as $path){
            if(is_dir($path)){
                self::$directories[] = $path;
                $dirContents = self::find_contents($path);
            } else {
                $path_errors[] = $path;
            }
        }

        if($path_errors){echo "The following directories do not exists<br />";die(var_dump($path_errors));}
    }

    // This is how we scan directories
    static private function find_contents($dir){
        $result = array();
        $root = scandir($dir);
        foreach($root as $value){
            if($value === '.' || $value === '..') {continue;}
            if(is_file($dir.DIRECTORY_SEPARATOR.$value)){
                if(!self::$ext_filter || in_array(strtolower(pathinfo($dir.DIRECTORY_SEPARATOR.$value, PATHINFO_EXTENSION)), self::$ext_filter)){
                    //self::$files[] = $result[] = $dir.DIRECTORY_SEPARATOR.$value;
                    self::$files[] = $result[] = $value;
                }
                continue;
            }
            if(self::$recursive){
                foreach(self::find_contents($dir.DIRECTORY_SEPARATOR.$value) as $value) {
                    self::$files[] = $result[] = $value;
                }
            }
        }
        // Return required for recursive search
        return $result;
    }
	
	/**
     * Get an array that represents directory tree
     * @param string $directory     Directory path
     * @param bool $recursive         Include sub directories
     * @param bool $listDirs         Include directories on listing
     * @param bool $listFiles         Include files on listing
     * @param regex $exclude         Exclude paths that matches this regex
     */
	 // use ScanDir\scan2(directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '');
    static function scan2($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '') {
        $arrayItems = array();
        $skipByExclude = false;
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
            //preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
            preg_match("/(^(([\.]){1,2})$|(\.(svn|git|htaccess|html))|(404\.md|Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
            if($exclude){
                preg_match($exclude, $file, $skipByExclude);
            }
            if (!$skip && !$skipByExclude) {
                if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                    if($recursive) {
                        $arrayItems = array_merge($arrayItems, self::scan2($directory. DIRECTORY_SEPARATOR . $file, $recursive, $listDirs, $listFiles, $exclude));
                    }
                    if($listDirs){
                        $file = $directory . DIRECTORY_SEPARATOR . $file;
						$d = explode(DIRECTORY_SEPARATOR,$file);
							//var_dump($d);
						   // $arrayItems[] = $file;
						   switch (count($d)) {
							case 2:
								$file = $d[1];
								break;
							case 3:
								$file = $d[1].'/'.$d[2];
								break;
							case 4:
								$file = $d[1].'/'.$d[2].'/'.$d[3];
								break;
							case 5:
								$file = $d[1].'/'.$d[2].'/'.$d[3].'/'.$d[4];
								break;
							default:
								$file = $d[1];
						}
					
                        $arrayItems[] = $file;
                    }
                } else {
                    if($listFiles){
                        $file = $directory . DIRECTORY_SEPARATOR . $file;
						$e = explode(DIRECTORY_SEPARATOR,$file);
							//var_dump($d);
						   // $arrayItems[] = $file;
						   switch (count($e)) {
							case 2:
								$file = $e[1];
								break;
							case 3:
								$file = $e[1].'/'.$e[2];
								break;
							case 4:
								$file = $e[1].'/'.$e[2].'/'.$e[3];
								break;
							case 5:
								$file = $e[1].'/'.$e[2].'/'.$e[3].'/'.$e[4];
								break;
							default:
								$file = $e[1];
						}
                        $arrayItems[] = $file;
                    }
                }
            }
        }
        closedir($handle);
        }
        return $arrayItems;
    }
	
	// category anly depth 1 subfolders
	public static function category($dir,$sub_category = false)
	{
		if($sub_category){
			//$directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = ''
			$cat =  self::scan2($dir,true, true,false);
		} else {
			$cat = self::scan2($dir,false, true,false);
		}
		
		return $cat;
	}
	// get folder with files in associative array
	public static function getAll($dir)
	{
		$result = array();
		foreach (new \DirectoryIterator($dir) as $fileInfo) {
			if (!$fileInfo->isDot()) {
				if ($fileInfo->isDir()) {
					$result[$fileInfo->getFilename()] = self::getAll($fileInfo->getPathname());
				} else {					
					if( $fileInfo->getFilename() != '.htaccess' && $fileInfo->getFilename() != 'index.html' && $fileInfo->getFilename() != '404.md' ){
						$result[] = $fileInfo->getFilename(); 
					}
				}
			}
		}
		return $result;
	}
}
/*
Usage:
scanDir::scan(path(s):string|array, [file_extensions:string|array], [subfolders?:true|false]);
<?php
//Scan a single directory for all files, no sub-directories
$files = scanDir::scan('D:\Websites\temp');

//Scan multiple directories for all files, no sub-dirs
$dirs = array(
    'D:\folder';
    'D:\folder2';
    'C:\Other';
);
$files = scanDir::scan($dirs);

// Scan multiple directories for files with provided file extension,
// no sub-dirs
$files = scanDir::scan($dirs, "jpg");
//or with an array of extensions
$file_ext = array(
    "jpg",
    "bmp",
    "png"
);
$files = scanDir::scan($dirs, $file_ext);

// Scan multiple directories for files with any extension,
// include files in recursive sub-folders
$files = scanDir::scan($dirs, false, true);

// Multiple dirs, with specified extensions, include sub-dir files
$files = scanDir::scan($dirs, $file_ext, true);
*/
