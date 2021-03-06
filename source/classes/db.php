<?php
class db {

	private $connection = null;

	public function __construct() {
		try {
			$this->connection = new PDO(PDO_DB_TYPE . ":host=" . PDO_DB_HOST . ";dbname=" . PDO_DB_NAME, PDO_DB_USER, PDO_DB_PASSWORD);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $ex) {
			if(DEBUG) echo $ex->getMessage();
			die();
		}
	}

	public function __destruct() {
		if($this->connection != null) {
			$this->connection = null;
		}
	}

	public function get_connection() {
		return $this->connection;
	}

}

?>
