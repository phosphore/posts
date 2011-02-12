<?php 
	require_once("../classes/AutoLoader.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <script src="/posts/source/libs/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="/posts/source/config/CONFIG.js" type="text/javascript"></script>
    <script src="/posts/source/js/utils.js" type="text/javascript"></script>
    <script src="/posts/source/js/topic.js" type="text/javascript"></script>
    <script src="/posts/source/js/ajax_topic.js" type="text/javascript"></script>
    <script src="/posts/source/js/textarea_format.js" type="text/javascript"></script> 
    <link href="/posts/source/css/common.css" type="text/css" rel="stylesheet" />
    <link href="/posts/source/css/form.css" type="text/css" rel="stylesheet" />
    <link href="/posts/source/css/topic.css" type="text/css" rel="stylesheet" />
    <link href="/posts/source/css/paging.css" type="text/css" rel="stylesheet" />
</head>

<body>

<noscript>
This page needs javascript to work properly.  You browser either has javascript disabled or does not support it.
</noscript>

<div id="content">

<div id="wrapper">
	<div id="cap">New Topic</div>
<div id="form">
    <fieldset>
     <div id="error_topic" style="display: none"></div>
      <p>
		<label for="author"><span class="required">*</span> Name:</label> 
		<input type="text" name="author" class="text" id="author" />
      </p>
	 <label for="title"><span class="required">*</span> Title:</label> 
	 <input type="text" name="title" class="text" id="title" />
      <p>
	 	<label for="msg"><span class="required">*</span> Message:</label> 
	 	<script type="text/javascript">FormatBar.topic_display('msg');</script>
	 	<textarea rows="1" cols="1" id="msg" name="msg"></textarea>
      </p>
      <input type="button" name="submit" id="submit" value="Post" />
    </fieldset>
</div>
</div>

<div id="posts">
<?php 
$topic = new Topic();
$pager = new Pager();
$topic_sql = new TopicRetrieval();
$count = $topic_sql->count();
		
if($count != 0) {	
	$query = $topic_sql->query_topic_with_limit();
	$_topic = $topic_sql->retrieveTopic($query);
      
	$post_xml = new PostXML();   
	for($i = 0; $i < count($_topic); $i++) {
		$post_xml->build_post_xml($_topic[$i]['pk_topic_id'], $_topic[$i]['title'], $topic->format_date($_topic[$i]['timestamp']), $_topic[$i]['author']);
	}
	
	echo $post_xml->transform();
	unset($post_xml);
?>
</div>

<?php
  	$xml_pager = new TopicXMLPager();
    $total_pages = $topic->total_pages($count);
    if($total_pages > 1) { 
   		echo "<div id='paging'>";
    	$pager->paging($total_pages,1);
    	$xml_pager->build_pager(1,1, $pager->total_pages(), $topic_sql->getEarliestDate());
    	echo $xml_pager->transform_pager();
    	echo "</div>";	
    }	
   
}
?>

<div id="data">
<?php
   $data = new TopicXMLData();
   $data->build_data($topic_sql->getLatestDate(), 1);
   echo $data->transform_data();
?>
</div>

</div>

</body>

</html>
