<?php
class Reply extends Post {

	public function __construct() {
		if(func_num_args() == 2) {
			list($author,$message) = func_get_args();
			parent::__construct($author,$message);
		}
	}

	public function addErrorMsg($error_msg) {
		$this->validation->addErrorMsg($error_msg);
	}
	
	public function error() {
		return $this->validation->error();
	}
	
	public function setError() {
		return $this->validation->setError();
	}

	public function getResponse() {
		return $this->validation->getResponse();
	}

	public function getAuthor() {
		return $this->author;
	}

	public function getMessage() {
		return $this->message;
	}

	public function total_pages($total_pgs) {
		return ceil($total_pgs/REPLIES_PER_PAGE);
	}

	public function adjust_paging($total_pgs) {
		$val = self::total_pages($total_pgs);
		$val++;
		return $val;
	}

	public function paging_offset($curr_pg) {
	 	return (REPLIES_PER_PAGE*($curr_pg-2));
	}

	public function format_date($timestamp) {
		return parent::format_date($timestamp);
	}

}

?>
