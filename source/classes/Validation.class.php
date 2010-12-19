<?php
namespace classes;

final class Validation {

	private $reply_id;
	private $parent_id;
	private $topic_id;
	private $latest_date;
	private $earliest_date;
	private $type;
	private $id;
	private $page;
	private $multiples_ids = array();
	private $response = array('error' => false,'error_msg' => array());

	function __construct() {}

	public function addErrorMsg($error_msg) {
		$this->response['error'] = true;
		$this->response['error_msg'][] = $error_msg;
	}

	public function error() {
		return $this->response['error'];
	}

	public function getResponse() {
		return $this->response;
	}

	public function isValidId($id) {
		if(is_numeric($id) && $id > 0) {
			return true;
		}
		return false;
	}

	public function isValidReplyId($id) {
		if($this->isValidId($id)) {
			$this->reply_id = (int) trim($this->sanitize($id));
			return true;
		}
		return false;
	}

	public function isValidTopicId($id) {
		if($this->isValidId($id)) {
			$this->topic_id = (int) trim($this->sanitize($id));
			return true;
		}
		return false;
	}

	public function isValidParentId($id) {
		if($this->isValidId($id)) {
			$this->parent_id = (int) trim($this->sanitize($id));
			return true;
		}
		return false;
	}

	public function isValidMultipleIds($ids) {
		foreach($ids as $key => $id) {
			if(!($this->isValidId($id))) {
				return false;
			}
			$this->multiple_ids[] = (int) trim($this->sanitize($id));
		}
		return true;
	}

	public function getMultipleIds() {
		return $this->multiple_ids;
	}

	public function isValidPage($page) {
		if(is_numeric($page) && $page > 0) {
			$this->page = (int) trim($this->sanitize($page));
			return true;
		}
		return false;
	}

	public function isValidParentIdOrNull($id) {
		if((strcmp($id,"null") == 0) || (is_numeric($id) && $id > 0)) {
			$this->parent_id = trim($this->sanitize($id));
			return true;
		}
		return false;
	}

	public function isValidType($type) {
		if((strcmp("reply_to_reply",$type) == 0) | (strcmp("reply_to_topic",$type) == 0) | (strcmp("topic",$type) == 0)) {
			$this->type = $type;
			return true;
		}
		return false;
	}

	public function isValidDate($date) {
		if(strtotime($date) === false) {
			return false;
		}
		return true;
	}

	public function isValidLatestDate($date) {
		if($this->isValidDate($date)) {
			$this->latest_date = $this->sanitize($date);
			return true;
		}
		return false;
	}

	public function isValidEearliestDate($date) {
		if($this->isValidDate($date)) {
			$this->earliest_date = $this->sanitize($date);
			return true;
		}
		return false;
	}

	public function sanitize($str) {
		return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
	}

	public function getLatestDate() {
		return $this->latest_date;
	}

	public function getEearliestDate() {
		return $this->earliest_date;
	}

	public function getType() {
		return $this->type;
	}

	public function getReplyId() {
		return $this->reply_id;
	}

	public function getId() {
		return $this->id;
	}

	public function getPage() {
		return $this->page;
	}

	public function getParentId() {
		return $this->parent_id;
	}

	public function getTopicId() {
		return $this->topic_id;
	}

}

?>
