<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../classes/AutoLoader.php");

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

$db = new db();
$bbcode = new BBCode();
$bbcode->SetAllowAmpersand(true);

try {
	$query_topic = $db->get_connection()->query(sprintf("select pk_topic_id, author, title, timestamp, message from topic where pk_topic_id=%s",$validation->getTopicId()));
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

$topic = new Topic();

$topic_xml = new TopicXML($validation->getPage());
 
while($_topic = $query_topic->fetch(PDO::FETCH_OBJ)) {
	$topic_xml->build_topic_xml(
	$_topic->pk_topic_id, 
	$_topic->title,
	$topic->format_date($_topic->timestamp), 
	$_topic->author,
	$bbcode->Parse($_topic->message)
	);
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

$reply_sql = new ReplyRetrieval();

$reply_xml = new ReplyXML();
 
while($result = $reply_result->fetch(PDO::FETCH_OBJ)) {
    $type = empty($result->fk_reply_id) ? "reply_to_topic" :  "reply_to_reply";
	$reply_xml->build_reply_xml(
	$result->pk_reply_id, 
	$reply->format_date($result->timestamp), 
	$result->author,
	$bbcode->Parse($result->message),
	$result->reply_to,$type
	);
}

$reply_xml->appendTopic($topic_xml->retrieveFirstChild());
$json_reply->comments =  $reply_xml->transform(); 

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

$xml_pager = new ReplyXMLPager();
$xml_pager->build_pager($validation->getTopicId(), $pager->start_page(), $validation->getPage(), $pager->total_pages(), $validation->getEearliestDate());
$json_reply->paging = $xml_pager->transform_pager();

$data = new ReplyXMLData();
$data->build_data($validation->getTopicId(), $validation->getEearliestDate(), $validation->getPage());
$json_reply->data = $data->transform_data();

if($reply_result->rowCount() == 0) {
	$no_result = array();
	$no_result = array("id" => $validation->getTopicId(), "pagefault" => true);
	echo json_encode($no_result);
} else {
	echo json_encode($json_reply);
}

unset($pager,$reply,$topic,$db,$json_reply,$bbcode);
?>
