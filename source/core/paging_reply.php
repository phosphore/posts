<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../libs/AutoLoader.php");
Autoloader::register();

use classes\Reply as Reply;
use classes\Pager as Pager;
use classes\db as db;
use classes\Topic as Topic;
use classes\Validation as Validation;

$reply = new Reply();

$validation = new Validation();
if(!(isset($_POST['topic_id'])) || !(isset($_POST['earliest_date'])) || !(isset($_POST['next_page'])) 
|| !($validation->isValidTopicId($_POST['topic_id']))
|| !($validation->isValidEearliestDate($_POST['earliest_date']))
|| !($validation->isValidPage($_POST['next_page']))
) {
	$validation->addErrorMsg("An error occured");
	echo json_encode($validation->getResponse());
	die();
}

$json_reply = new stdClass;
$json_reply->comments = array();
$json_reply->paging = array();
$json_reply->data = array();

$db = new db();

try {
	$query_topic = $db->get_connection()->query(sprintf("select pk_topic_id, author, title, timestamp, message from topic where pk_topic_id=%s",$validation->getTopicId()));
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

$topic = new Topic();

while($_topic = $query_topic->fetch(PDO::FETCH_OBJ)) {
	if($validation->getPage() == 1) {
		$json_reply->comments[] = sprintf("
		<div class=\"topic\" id=\"topic_%s\" onmouseover='Reply.show_button(\"topic_%s\")' onmouseout='Reply.hide_button(\"topic_%s\")'>
		<span class=\"title\">%s</span>
		<span class=\"date\">%s</span>
		<span class=\"author_name\">%s</span>
		<span class=\"message\">%s
		<button onclick=\"Reply.pre_reply('topic_%s');\" type=\"button\" class=\"reply_btn\">reply</button>
		</span></div>",
		$_topic->pk_topic_id,$_topic->pk_topic_id,$_topic->pk_topic_id,$_topic->title, $topic->format_date($_topic->timestamp),$_topic->author,$_topic->message,$_topic->pk_topic_id);
	} else {
		$json_reply->comments[] = sprintf("
		<div class=\"topic\" id=\"topic_%s\">
		<span class=\"title\">%s</span>
		<span class=\"date\">%s</span>
		<span class=\"author_name\">%s</span>
		<span class=\"message\">%s
		<button onclick=\"Reply.pre_reply('topic_%s');\" type=\"button\" class=\"reply_btn\">reply</button>
		</span></div>",
		$_topic->pk_topic_id,$_topic->title, $topic->format_date($_topic->timestamp),$_topic->author,$_topic->message,$_topic->pk_topic_id);
	}
}

$offset = $reply->paging_offset($validation->getPage());
if($validation->getPage() == 1) {
	$sql_reply = sprintf("select reply.pk_reply_id, reply.fk_topic_id, reply.pk_reply_id, reply.timestamp, reply.author, reply_to.author AS reply_to, reply.message, reply.parent, reply.fk_reply_id from reply as reply left join reply reply_to on reply.fk_reply_id=reply_to.pk_reply_id where reply.parent IN (select pk_reply_id from reply where fk_reply_id is null and timestamp >= '%s' and fk_topic_id=%s) OR reply.pk_reply_id IN(select pk_reply_id from reply where fk_reply_id is null and timestamp >= '%s' and fk_topic_id=%s) order by reply.position",$validation->getEearliestDate(),$validation->getTopicId(),$validation->getEearliestDate(),$validation->getTopicId());
} else {
	$sql_reply = sprintf("select reply.pk_reply_id, reply.fk_topic_id, reply.pk_reply_id, reply.timestamp, reply.author, reply_to.author AS reply_to, reply.message, reply.parent, reply.fk_reply_id from reply as reply left join reply reply_to on reply.fk_reply_id=reply_to.pk_reply_id where reply.parent IN (select pk_reply_id from reply where fk_reply_id is null and timestamp < '%s' and fk_topic_id=%s order by timestamp desc offset %s limit %s) OR reply.pk_reply_id IN(select pk_reply_id from reply where fk_reply_id is null and timestamp < '%s' and fk_topic_id=%s order by timestamp desc offset %s limit %s) order by reply.position",$validation->getEearliestDate(),$validation->getTopicId(),$offset,REPLIES_PER_PAGE,$validation->getEearliestDate(),$validation->getTopicId(),$offset,REPLIES_PER_PAGE);
}

try {
	$reply_result = $db->get_connection()->query($sql_reply);
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

while($rs = $reply_result->fetch(PDO::FETCH_OBJ)) {
	if(empty($rs->fk_reply_id)) {
		$json_reply->comments[] = sprintf("<div id=\"reply_%s\" onmouseover='Reply.show_button(\"reply_%s\")'  onmouseout='Reply.hide_button(\"reply_%s\")' class=\"reply_to_topic\"><span class=\"reply_to_topic_tri\"></span><span class=\"top_tri\"></span><span class=\"date\">%s</span><span class=\"author_name\">%s</span><span class=\"message\">%s<button type=\"button\" class=\"reply_btn\" onclick=\"Reply.pre_reply('reply_%s');\">reply</button></span></div>",
		$rs->pk_reply_id,$rs->pk_reply_id,$rs->pk_reply_id,$reply->format_date($rs->timestamp),$rs->author,$rs->message,$rs->pk_reply_id);
	} else {
		$json_reply->comments[] = sprintf("<div id=\"reply_%s\" onmouseover='Reply.show_button(\"reply_%s\")'  onmouseout='Reply.hide_button(\"reply_%s\")' class=\"reply_to_reply\"><span class=\"reply_to_reply_tri\"></span><span class=\"top_tri\"></span><span class=\"reply_to_author\">@%s</span><span class=\"date\">%s</span><span class=\"author_name\">%s</span><span class=\"message\">%s<button type=\"button\" class=\"reply_btn\" onclick=\"Reply.pre_reply('reply_%s');\">reply</button></span></div>",
		$rs->pk_reply_id,$rs->pk_reply_id,$rs->pk_reply_id,$rs->reply_to,$reply->format_date($rs->timestamp),$rs->author,$rs->message,$rs->pk_reply_id);
	}
}

$sql_reply_count = sprintf("select count(*) from reply where fk_topic_id=%s and fk_reply_id is null and timestamp < '%s'",$validation->getTopicId(),$validation->getEearliestDate()); // changed from global

try {
	$count_result = $db->get_connection()->query($sql_reply_count);
	list($count) = $count_result->fetch();
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

$total_pages = $reply->adjust_paging($count);

$pager = new Pager();
$pager->paging($total_pages,$validation->getPage());
$i = $pager->start_page();
$pages = $pager->total_pages();

while($i <= $pages) {
	if ($i==$validation->getPage()) {
		$json_reply->paging[] = "<button type=\"button\" id=\"curr_pg\">$i</button>";
	} else {
		$json_reply->paging[] = "<button class=\"paging_btns\" onClick=\"AjaxReply.paging_reply(" . $validation->getTopicId() . "," . $validation->getPage() . ",$i,'" .$validation->getEearliestDate() ."')\">$i</button>";
	}
	$i++;
}

$json_reply->data[] = "<div id='current_pg' title=" . $validation->getPage() . "></div>";
$json_reply->data[] = "<div id='topic_id' title=" . $validation->getTopicId() . "></div>";
$json_reply->data[] = "<div id=\"earliest_date\" title='" . $validation->getEearliestDate() . "'></div>";

if($reply_result->rowCount() == 0) {
	$no_result = array();
	$no_result = array("id" => $validation->getTopicId(), "pagefault" => true);
	echo json_encode($no_result);
} else {
	echo json_encode($json_reply);
}

unset($pager,$reply,$topic,$db,$json_reply);
?>
