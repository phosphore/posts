<?php
namespace classes\XML;

use DOMElement;
use DOMDocument;
use XSLTProcessor;

class PostXML extends XMLCommon {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function build_post_xml($id,$title,$date,$author) {
		parent::setNodeName("post");
		parent::build_xml($id, $date, $author);
		$this->post->setAttribute('title', $title);
	}
	
	public function transform() {
		return parent::transform();
	}
	
}

?>