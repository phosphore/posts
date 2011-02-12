<?php
require_once("../classes/AutoLoader.php");

$validation = new Validation();
if(!(isset($_GET['id'])) || !($validation->isValidTopicId($_GET['id']))) {
	header("Location: " . BASE_URL);
	die();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Posts</title>
	<script src="/posts/source/config/CONFIG.js" type="text/javascript"></script>
	<script src="/posts/source/libs/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="/posts/source/js/utils.js" type="text/javascript"></script>
    <script src="/posts/source/libs/json2.js" type="text/javascript"></script>
	<script src="/posts/source/js/update_replies.js" type="text/javascript"></script>
   	<script src="/posts/source/js/ajax_reply.js" type="text/javascript"></script>
 	<script src="/posts/source/js/textarea_format.js" type="text/javascript"></script>
    <script src="/posts/source/js/reply.js" type="text/javascript"></script>
    <link href="/posts/source/css/common.css" type="text/css" rel="stylesheet" />
    <link href="/posts/source/css/reply.css" type="text/css" rel="stylesheet" />
    <link href="/posts/source/css/paging.css" type="text/css" rel="stylesheet" />
</head>

<body>

<noscript>
This page needs javascript to work properly.  You browser either has javascript disabled or does not support it.
</noscript>

<div id="content">

<span class="new_post"><a href="<?php echo BASE_URL ?>">Return to topics</a></span>
	 
<div id="posts">
<?php 
    $reply = new Reply();
    $topic = new Topic();
    $topic_sql = new TopicRetrieval();
    $reply_sql = new ReplyRetrieval();
    $bbcode = new BBCode();
	$bbcode->SetAllowAmpersand(true);

    $query_topic = $topic_sql->query_topic_by_id($validation->getTopicId());
    $_topic = $topic_sql->retrieveTopic($query_topic);

  	$topic_xml = new TopicXML(1);
 
	for($i = 0; $i < count($_topic); $i++) {
		$tt = $bbcode->Parse($_topic[$i]['message']);
		$topic_xml->build_topic_xml($_topic[$i]['pk_topic_id'], $_topic[$i]['title'], $topic->format_date($_topic[$i]['timestamp']), $_topic[$i]['author'],$tt);	
	}

	echo $topic_xml->transform(); 
   
    $count = $reply_sql->count($validation->getTopicId());
    
if($count != 0) {

	$query_reply = $reply_sql->query_reply($validation->getTopicId());
	$_reply = $reply_sql->retrieveReply($query_reply);
	
	$reply_xml = new ReplyXML();

	for($i = 0; $i < count($_reply); $i++) {
		$reply_xml->build_reply_xml($_reply[$i]['pk_reply_id'], $reply->format_date($_reply[$i]['timestamp']), $_reply[$i]['author'],$bbcode->Parse($_reply[$i]['message']),$_reply[$i]['reply_to'],$_reply[$i]['type']);
	}

	echo $reply_xml->transform(); 
?>
</div>

<?php 
	$total_pages = $reply->total_pages($count);
    $pager = new Pager();
    $pager->paging($total_pages,1);
    
    if($total_pages > 1) {
		echo "<div id='paging'>";
   	 	$xml_pager = new ReplyXMLPager();
    	$xml_pager->build_pager($validation->getTopicId(), 1, 1, $pager->total_pages(), $reply_sql->getEearliestDate());
   		echo $xml_pager->transform_pager();
   		echo "</div>";
	}

}
?>

<div id="data">
<?php 
	$data = new ReplyXMLData();
	$data->build_data($validation->getTopicId(), $reply_sql->getEearliestDate(), 1);
	echo $data->transform_data();
?>
</div>

</div>

</body>

</html>
