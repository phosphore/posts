<?php
include_once("../inc/CONFIG.inc.php");
include_once("../class/Validation.class.php");

$validation = new Validation();
if(!(isset($_GET['id'])) || !($validation->isValidTopicId($_GET['id']))) {
	header("Location: " . BASE_URL);
	die();
}
?>

<html>
<head>
	<title>Posts</title>
    <script src="../libs/jquery-1.4.3.min.js" type="text/javascript"></script>
    <script src="../js/CONFIG.js" type="text/javascript"></script>
    <script src="../js/utils.js" type="text/javascript"></script>
    <script src="../libs/json2.js" type="text/javascript"></script>
    <script src="../js/update_replies.js" type="text/javascript"></script>
    <script src="../js/ajax_reply.js" type="text/javascript"></script>
    <script src="../js/reply.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../css/common.css" type="text/css" />
    <link rel="stylesheet" href="../css/reply.css" type="text/css" />
    <link rel="stylesheet" href="../css/paging.css" type="text/css" />
</head>

<body>

<div id="content">

<span class="new_post"><a href="<?php echo BASE_URL ?>">Return to topics</a></span>

<?php
    include_once("../class/PDOFactory.class.php");
    include_once("../class/Post.class.php");
    include_once("../class/Topic.class.php");
    include_once("../class/Reply.class.php");
    include_once("../class/Pager.class.php");
    include_once("../class/TopicRetrieval.class.php");
    include_once("../class/ReplyRetrieval.class.php");
    include_once("../class/Utils.class.php");
?>

<div id="posts">
<?php 
    $reply = new Reply();
    $topic = new Topic();
    $topic_sql = new TopicRetrieval();
    $reply_sql = new ReplyRetrieval();

    $query_topic = $topic_sql->query_topic_by_id($validation->getTopicId());
    $_topic = $topic_sql->retrieveTopic($query_topic);

    for($i = 0; $i < count($_topic); $i++) {
		echo "\t";
	  	echo sprintf("<div id=\"topic_%s\" class=\"topic\" onmouseover='Reply.show_button(\"topic_%s\")'  onmouseout='Reply.hide_button(\"topic_%s\")'>
	  	<span class=\"title\">%s</span><span class=\"date\">%s</span><span class=\"author_name\">%s</span>
	  	<span class=\"message\">%s<button onclick=\"Reply.pre_reply('topic_%s');\" type=\"button\" class=\"reply_btn\">reply</button></span></div>",
	  	$_topic[$i]['pk_topic_id'],$_topic[$i]['pk_topic_id'],$_topic[$i]['pk_topic_id'],$_topic[$i]['title'],$topic->format_date($_topic[$i]['timestamp']),$_topic[$i]['author'],$_topic[$i]['message'],$_topic[$i]['pk_topic_id']);
		echo "\n";
    }

    $count = $reply_sql->count($validation->getTopicId());
    if($count != 0) {

	$query_reply = $reply_sql->query_reply($validation->getTopicId());
	$_reply = $reply_sql->retrieveReply($query_reply);

	for($i = 0; $i < count($_reply); $i++) {
	    echo "\t";
	    if($reply_sql->is_reply_to_topic($_reply[$i]['fk_reply_id'])) {
			echo sprintf("<div id=\"reply_%s\" class=\"reply_to_topic\" onmouseover='Reply.show_button(\"reply_%s\")'  onmouseout='Reply.hide_button(\"reply_%s\")'>
			<span class=\"reply_to_topic_tri\"></span><span class=\"top_tri\"></span><span class=\"date\">%s</span><span class=\"author_name\">%s</span>
			<span class=\"message\">%s<button type=\"button\" class=\"reply_btn\" onclick=\"Reply.pre_reply('reply_%s');\">reply</button></span></div>",
			$_reply[$i]['pk_reply_id'],$_reply[$i]['pk_reply_id'],$_reply[$i]['pk_reply_id'],$reply->format_date($_reply[$i]['timestamp']),$_reply[$i]['author'],$_reply[$i]['message'],$_reply[$i]['pk_reply_id']);
			
			$reply_sql->setEearliestDate($_reply[$i]['timestamp']);
	    } else {
			echo sprintf("<div id=\"reply_%s\" class=\"reply_to_reply\" onmouseover='Reply.show_button(\"reply_%s\")'  onmouseout='Reply.hide_button(\"reply_%s\")'><span class=\"reply_to_reply_tri\"></span><span class=\"top_tri\"></span><span class=\"reply_to_author\">@%s</span><span class=\"date\">%s</span><span class=\"author_name\">%s</span><span class=\"message\">%s<button type=\"button\" class=\"reply_btn\" onclick=\"Reply.pre_reply('reply_%s');\">reply</button></span></div>",
			$_reply[$i]['pk_reply_id'],$_reply[$i]['pk_reply_id'],$_reply[$i]['pk_reply_id'],$_reply[$i]['reply_to'],$reply->format_date($_reply[$i]['timestamp']),$_reply[$i]['author'],$_reply[$i]['message'],$_reply[$i]['pk_reply_id']);
	    }
	    echo "\n";
	}
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
