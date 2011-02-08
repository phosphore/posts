<?php
class ReplyXMLData extends XMLCommon {
	
	public function __construct() {
		parent::__construct();
		$this->stylesheet = "/posts/source/xslt/reply_data.xsl";
	}
	
	public function build_data($topic_id,$earliest_date,$current_page) {
		$this->post = new DOMElement("data"); 
		$this->root->appendChild($this->post);  
		$this->post->setAttribute('earliest_date', $earliest_date);
		$this->post->setAttribute('topic_id', $topic_id);
		$this->post->setAttribute('current_page', $current_page);
	}
	
	public function transform_data() {
		return parent::transform();
	}
	
}
?>
