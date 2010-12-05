<?php
abstract class Post {

	abstract protected function total_pages($total_pgs); 
	abstract protected function adjust_paging($total_pgs); 
	abstract protected function paging_offset($curr_pg);
	protected $validation;

	protected $author;
	protected $message;

	public function __construct($author,$message) {
		$this->validation = new Validation();
		if($this->setAuthor($author)) {
			$this->author = $this->validation->sanitize($author);
		}
		if($this->setMessage($message)) {
			$this->message = $this->validation->sanitize($message);
		}
	}
	
	public function __destruct() {
		if($this->validation != null) {
			$this->validation = null;
		}
	}

	protected function setAuthor($author) {
		if(empty($author) || strlen($author) < 2 || strlen($author) > 25 || strcmp($author,"(enter your name)") == 0) {
			if(empty($author) || strcmp($author,"(enter your name)") == 0) {
				$this->validation->addErrorMsg("Enter a author");
			}
			if((strlen($author) < 2 || strlen($author) > 25) && !(empty($author))) {
				$this->validation->addErrorMsg("Author must be between 2 and 15 characters");
			}
			return false;
		}
		return true;
	}

	protected function setMessage($message) {
		if(empty($message) || strcmp($message,"(enter your message)") == 0 || strlen($message) > 200) {
			if(empty($message) || strcmp($message,"(enter your message)") == 0) {
				$this->validation->addErrorMsg("Enter your message");
			}
			if(strlen($message) > 200) {
				$this->validation->addErrorMsg("Message cannot exceed 200 characters");
			}
			return false;
		}
		return true;
	}

	protected function format_date($timestamp) {
		$date = new DateTime(date($timestamp));
		return $date->format("d.m.y g:ia");
	}

}
?>
