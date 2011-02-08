<?php
abstract class XMLCommon {
	
	protected $doc;
	protected $root;
	protected $xsl_doc;
	protected $xsl;
	protected $post;
	protected $node_name = "post";
	protected $stylesheet = "";
	
	public function __construct() {
		$this->root = new DOMElement('posts');
		$this->doc = new DOMDocument();
		$this->doc->appendChild($this->root);
		$this->xsl_doc = new DOMDocument();
		$this->xsl = new XSLTProcessor();
	}
	
	protected function setNodeName($name) {
		$this->node_name = $name;
	}
	
	protected function build_xml($id,$date,$author) {
		$this->post = new DOMElement($this->node_name);
		$this->root->appendChild($this->post);
		$this->post->setAttribute('id', $id);
		$this->post->setAttribute('date', $date);
		$this->post->setAttribute('author', $author);
	}
	
	protected function transform() {
		$this->xsl_doc->load($this->stylesheet);
		$this->xsl->importStylesheet($this->xsl_doc);
		return $this->xsl->transformToXML($this->doc);
	}

	protected function saveXML($xml) {
		$this->doc->formatOutput = TRUE;
		$this->doc->save($xml);
	}
	
}

?>