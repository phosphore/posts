<?php
namespace classes\XML;

use DOMElement;
use DOMDocument;
use XSLTProcessor;

class XMLCommon {
	
	protected $doc;
	protected $root;
	protected $xsl_doc;
	protected $xsl;
	protected $post;
	protected $node_name = "post";
	
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
	
	public function build_xml($id,$date,$author) {
		$this->post = new DOMElement($this->node_name);
		$this->root->appendChild($this->post);
		$this->post->setAttribute('id', $id);
		$this->post->setAttribute('date', $date);
		$this->post->setAttribute('author', $author);
	}
	
	public function transform() {
		$this->xsl_doc->load('/posts/source/xslt/posts.xsl');
		$this->xsl->importStylesheet($this->xsl_doc);
		//$this->doc->save("/posts/source/xml/xml.xml");
		return $this->xsl->transformToXML($this->doc);
	}
	
}

?>