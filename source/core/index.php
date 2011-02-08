<?php 
	require_once("../libs/AutoLoader.php");
?>

<html>
<head>
    <title>Posts</title>
    <script src="../libs/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="../config/CONFIG.js" type="text/javascript"></script>
    <script src="../js/utils.js" type="text/javascript"></script>
    <script src="../js/topic.js" type="text/javascript"></script>
    <script src="../js/ajax_topic.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/common.css" type="text/css" />
    <link rel="stylesheet" href="../css/form.css" type="text/css" />
    <link rel="stylesheet" href="../css/topic.css" type="text/css" />
    <link rel="stylesheet" href="../css/paging.css" type="text/css" />
</head>

<body>

<noscript>
This page needs javascript to work properly.  You browser either has javascript disabled or does not support it.
</noscript>

<div id="content">

<div id="wrapper">
<div id="form">
    <fieldset>
      <legend>Create a New Topic</legend>
      <div id="error_topic" style="display: none"></div>
      <p>
	<label for="author"><span class="required">*</span> Name:</label> 
	<input type="text" name="author" class="text" id="author" />
      </p>
      <p>
	 <label for="title"><span class="required">*</span> Title:</label> 
	 <input type="text" name="title" class="text" id="title" />
      </p>
      <p>
	 <label for="msg"><span class="required">*</span> Message:</label> 
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

<div id="paging">
<?php
  	$xml_pager = new TopicXMLPager();
    $total_pages = $topic->total_pages($count); 
   	$pager->paging($total_pages,1);
		
    $xml_pager->build_pager(1,1, $pager->total_pages(), $topic_sql->getEarliestDate());
    
    echo $xml_pager->transform_pager();		
}
?>
</div>


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
