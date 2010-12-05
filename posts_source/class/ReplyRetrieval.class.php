<?php
class ReplyRetrieval {

	private $earliest_date;
	private $db;

	public function __construct() {
		$this->db = new PDOFactory();
		$this->earliest_date = Utils::create_timestamp();
	}
	
	public function __destruct() {
		if($this->db != null) {
			$this->db = null;
		}
	}
	
	public function setEearliestDate($date) {
		$this->earliest_date = $date;
	}
	
	public function query_reply($id) {
		$sql_reply = sprintf("select reply.pk_reply_id, reply.fk_topic_id, reply.pk_reply_id, reply.timestamp, reply.author, reply_to.author AS reply_to, reply.message, reply.parent, reply.fk_reply_id from reply as reply left join reply reply_to on reply.fk_reply_id=reply_to.pk_reply_id where reply.parent IN (select pk_reply_id from reply where fk_reply_id is null and fk_topic_id=%s order by timestamp desc offset %s limit %s) OR reply.pk_reply_id IN(select pk_reply_id from reply where fk_reply_id is null and fk_topic_id=%s order by timestamp desc offset %s limit %s) order by reply.position",
		pg_escape_string($id),0,REPLIES_PER_PAGE,pg_escape_string($id),0,REPLIES_PER_PAGE);

		try {
			return $this->db->get_connection()->query($sql_reply);
		} catch (PDOException $e) {
			if(DEBUG) echo $e->getMessage();
			die();
		}
	}
	
	public function retrieveReply($query) {
		$i = 0;
		while($_reply = $query->fetch(PDO::FETCH_OBJ)) {
			$return[$i]['pk_reply_id'] = $_reply->pk_reply_id;
			$return[$i]['timestamp'] = $_reply->timestamp;
			$return[$i]['author'] = $_reply->author;
			$return[$i]['message'] = $_reply->message;
			$return[$i]['reply_to'] = $_reply->reply_to;
			$return[$i]['fk_reply_id'] = $_reply->fk_reply_id;
			++$i;		
		}
		return $return;
	}

	public function count($id) {
		try {
			list($count) = $this->db->get_connection()->query(sprintf("select count(*) from reply where fk_reply_id IS NULL and fk_topic_id=%s",pg_escape_string($id)))->fetch();
			return $count;
		} catch (PDOException $e) {
			if(DEBUG) echo $e->getMessage();
			die();
		}
	}
	
	public function is_reply_to_topic($fk_reply_id) {
		return empty($fk_reply_id);
	}
	
	public function getEearliestDate() {
		return $this->earliest_date;
	}	

}

?>
