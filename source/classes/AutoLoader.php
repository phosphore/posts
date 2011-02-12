<?php
require_once '../config/CONFIG.php';

final class Autoloader {

	static public function register() {
		spl_autoload_register(array(new self, 'load'));
	}

	public static function load($class) {
	 $paths = explode(PATH_SEPARATOR, get_include_path());

	 foreach($paths as $path) {
	 	if(file_exists($file = $path.'/'.$class.'.php')) {
	 		require_once($file);
	 	}
	 }

	}

}

Autoloader::register();
?>