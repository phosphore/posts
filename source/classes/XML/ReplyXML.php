<?php
class ReplyXML extends XMLCommon {

	public function __construct() {
		parent::__construct();
		$this->stylesheet = "/posts/source/xslt/posts.xsl";
	}

	public function build_reply_xml($id,$date,$author,$message, $reply_to, $type) {
		parent::setNodeName("reply");
		parent::build_xml($id, $date, $author);
		$this->post->setAttribute('message', $message);
		if(strcmp($type,"reply_to_reply") == 0) {
			$this->post->setAttribute('reply_to', $reply_to);
		}
		$this->post->setAttribute('type', $type);
	}
	
	public function appendTopic($node) {
		$this->doc->appendChild($this->doc->importNode($node,true));
	}
	
	public function transform() {
		return parent::transform($this->stylesheet);
	}
	
}

?>
