<?php
namespace classes;

class Topic extends Post {

	private $title;

	public function __construct() {
		if(func_num_args() == 3) {
			list($title,$author,$message) = func_get_args();
			parent::__construct($author,$message);

			if($this->setTitle($title)) {
				$this->title = $this->validation->sanitize($title);
			}
		}
	}

	public function error() {
		return $this->validation->error();
	}

	public function getResponse() {
		return $this->validation->getResponse();
	}

	public function getTitle() {
		return $this->title;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function getMessage() {
		return $this->message;
	}

	public function setTitle($title) {
		if(empty($title) || strlen($title) < 2 || strlen($title) > 45) {
			if(empty($title)) {
				$this->validation->addErrorMsg("Enter a title");
			}
			if((strlen($title) < 2 || strlen($title) > 45) && !(empty($title))) {
				$this->validation->addErrorMsg("Title must be between 2 and 45 characters");
			}
			return false;
		}
		return true;
	}

	public function total_pages($total_pgs) {
		return ceil($total_pgs/TOPICS_PER_PAGE);
	}

	public function adjust_paging($total_pgs) {
		$val = self::total_pages($total_pgs);
		$val++;
		return $val;
	}

	public function paging_offset($curr_pg) {
		return (TOPICS_PER_PAGE*($curr_pg-2));
	}

	public function format_date($timestamp) {
		return parent::format_date($timestamp);
	}
	
}
?>
