<?php
class TopicXMLData extends XMLCommon {
	
	public function __construct() {
		parent::__construct();
		$this->stylesheet = "/posts/source/xslt/topic_data.xsl";
	}
	
	public function build_data($latest_date,$current_page) {
		$this->post = new DOMElement("data"); 
		$this->root->appendChild($this->post);  
		$this->post->setAttribute('latest_date', $latest_date);
		$this->post->setAttribute('current_page', $current_page);
	}
	
	public function transform_data() {
		return parent::transform();
	}
	
}

?>
