<?php
class TopicXMLPager extends XMLCommon {
	
	public function __construct() {
		parent::__construct();
		$this->stylesheet = "/posts/source/xslt/topic_pager.xsl";
	}
	
	public function build_pager($start_pg,$current_pg,$total_pages,$earliest_date) {
		$this->post = new DOMElement("pager"); 
		$this->root->appendChild($this->post);  
		$this->post->setAttribute('start_pg', $start_pg);
		$this->post->setAttribute('current_pg', $current_pg);
		$this->post->setAttribute('total_pages', $total_pages);
		$this->post->setAttribute('earliest_date', $earliest_date);
	}
	
	public function transform_pager() {
		return parent::transform();
	}
	
}

?>
