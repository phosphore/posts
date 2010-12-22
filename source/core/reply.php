<?php
require_once("../libs/AutoLoader.php");
Autoloader::register();

use classes\Validation as Validation;
use classes\Reply as Reply;
use classes\Topic as Topic;
use classes\TopicRetrieval as TopicRetrieval;
use classes\ReplyRetrieval as ReplyRetrieval;
use classes\Pager as Pager;
use classes\XML\TopicXML as TopicXML;
use classes\XML\ReplyXML as ReplyXML;

$validation = new Validation();
if(!(isset($_GET['id'])) || !($validation->isValidTopicId($_GET['id']))) {
	header("Location: " . BASE_URL);
	die();
}
?>

<html>
<head>
	<title>Posts</title>
    <script src="/posts/source/libs/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="/posts/source/config/CONFIG.js" type="text/javascript"></script>
    <script src="/posts/source/js/utils.js" type="text/javascript"></script>
    <script src="/posts/source/libs/json2.js" type="text/javascript"></script>
    <script src="/posts/source/js/update_replies.js" type="text/javascript"></script>
    <script src="/posts/source/js/ajax_reply.js" type="text/javascript"></script>
    <script src="/posts/source/js/reply.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/posts/source/css/common.css" type="text/css" />
    <link rel="stylesheet" href="/posts/source/css/reply.css" type="text/css" />
    <link rel="stylesheet" href="/posts/source/css/paging.css" type="text/css" />
</head>

<body>

<div id="content">

<span class="new_post"><a href="<?php echo BASE_URL ?>">Return to topics</a></span>

<div id="posts">
<?php 
    $reply = new Reply();
    $topic = new Topic();
    $topic_sql = new TopicRetrieval();
    $reply_sql = new ReplyRetrieval();

    $query_topic = $topic_sql->query_topic_by_id($validation->getTopicId());
    $_topic = $topic_sql->retrieveTopic($query_topic);

  	$topic_xml = new TopicXML(1);
 
	for($i = 0; $i < count($_topic); $i++) {
		$topic_xml->build_topic_xml($_topic[$i]['pk_topic_id'], $_topic[$i]['title'], $topic->format_date($_topic[$i]['timestamp']), $_topic[$i]['author'],$_topic[$i]['message']);
	}

	echo $topic_xml->transform(); 
   
    $count = $reply_sql->count($validation->getTopicId());
    if($count != 0) {

	$query_reply = $reply_sql->query_reply($validation->getTopicId());
	$_reply = $reply_sql->retrieveReply($query_reply);
	
	$reply_xml = new ReplyXML();
 
	for($i = 0; $i < count($_reply); $i++) {
		$reply_xml->build_reply_xml($_reply[$i]['pk_reply_id'], $reply->format_date($_reply[$i]['timestamp']), $_reply[$i]['author'],$_reply[$i]['message'],$_reply[$i]['reply_to'],$_reply[$i]['type']);
	}

	echo $reply_xml->transform(); 
?>
</div>
	
<div id="paging">
<?php 
    $total_pages = $reply->total_pages($count);
	
    $pager = new Pager();
    $pager->paging($total_pages,1);
    $i = $pager->start_page();
    $pages = $pager->total_pages();
	
    if($pages != 1) {
		while($i <= $pages) {
	    	if ($i==1) {
				echo "<button type=\"button\" id=\"curr_pg\">$i</button>";
	    	} else {
				echo "<button class=\"paging_btns\" onClick=\"AjaxReply.paging_reply(" .$validation->getTopicId() .",1,$i,'" . $reply_sql->getEearliestDate() ."')\">$i</button>";
	    	}
	    	$i++;
		}
    }
	
    }
?>
</div>
	
<div id="data">
<?php 
    echo "<div id='current_pg' title='1'></div>";
    echo "<div id='earliest_date' title='" . $reply_sql->getEearliestDate() ."'></div>";
    echo "<div id='topic_id' title=" . $validation->getTopicId() ."></div>";
?>
</div>

</div>

</body>

</html>
