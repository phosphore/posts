<?php
namespace classes;

use classes\db as pf;
use classes\Utils as u;
use PDO;

class TopicRetrieval {

	private $latest_date;
	private $earliest_date;
	private $db;

	public function __construct() {
		//$this->db = new PDOFactory();
		$this->db = new pf();
		//$this->latest_date = Utils::create_timestamp();
		$this->latest_date = u::create_timestamp();
	}
	
	public function __destruct() {
		if($this->db != null) {
			$this->db = null;
		}
	}

	public function query_topic_with_limit() {
		try {
			return $this->db->get_connection()->query(sprintf("select pk_topic_id, title, timestamp, message, author from topic order by timestamp desc offset 0 limit %s",TOPICS_PER_PAGE));
		} catch (PDOException $e) {
			if(DEBUG) echo $e->getMessage();
			die();
		}
	}

	public function query_topic_by_id($id) {
		try {
			return $this->db->get_connection()->query(sprintf("select pk_topic_id, title, timestamp, message, author from topic where pk_topic_id=%s",pg_escape_string($id)));
		} catch (PDOException $e) {
			if(DEBUG) echo $e->getMessage();
			die();
		}
	}

	public function retrieveTopic($query_topic) {
		$i = 0;
		$return = array();
		while($_topic = $query_topic->fetch(PDO::FETCH_OBJ)) {
			if($i == 0) {
				$this->latest_date = $_topic->timestamp;
			}
			$return[$i]['pk_topic_id'] = $_topic->pk_topic_id;
			$return[$i]['title'] = $_topic->title;
			$return[$i]['timestamp'] = $_topic->timestamp;
			$return[$i]['author'] = $_topic->author;
			$return[$i]['message'] = $_topic->message;
			++$i;
			$this->earliest_date = $_topic->timestamp;
		}
		return $return;
	}

	public function count() {
		try {
			list($count) = $this->db->get_connection()->query("select count(*) from topic")->fetch();
			return $count;
		} catch (PDOException $e) {
			if(DEBUG) echo $e->getMessage();
			die();
		}
	}

	public function getEarliestDate() {
		return $this->earliest_date;
	}

	public function getLatestDate() {
		return $this->latest_date;
	}

}

?>
