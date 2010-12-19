<?php
final class Autoloader {

	static public function register() {
		if (file_exists($file = "../config/CONFIG.php")) {
			require $file;
		}
		spl_autoload_register(array(new self, 'autoload'));
	}

	static public function autoload($class) {	
		if (file_exists($file = "../" . str_replace('\\', '/', $class) . '.class.php')) {
			require $file;
		}
	}
	
}

?>