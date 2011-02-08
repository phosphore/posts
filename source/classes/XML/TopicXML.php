<?php
class TopicXML extends XMLCommon {

	public function __construct($pg) {
		parent::__construct();
		$this->root->setAttribute('pg_num', $pg);
		$this->stylesheet = "/posts/source/xslt/posts.xsl";
	}

	public function build_topic_xml($id,$title,$date,$author,$message) {
		parent::setNodeName("topic");
		parent::build_xml($id, $date, $author);
		$this->post->setAttribute('title', $title);
		$this->post->setAttribute('message', $message);
	}
	
	public function retrieveFirstChild() {
		return $this->doc->firstChild;
	}

	public function transform() {
		return parent::transform($this->stylesheet);
	}
}

?>