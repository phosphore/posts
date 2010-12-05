<?php
header("Content-Type: application/json; charset=UTF-8");

include_once("../inc/CONFIG.inc.php");
include_once("../class/Post.class.php");
include_once("../class/Topic.class.php");
include_once("../class/PDOFactory.class.php");
include_once("../class/Pager.class.php");
include_once("../class/Validation.class.php");

$validation = new Validation();
if(!(isset($_POST['next_pg'])) || !(isset($_POST['earliest_date'])) || !(isset($_POST['latest_date'])) 
|| !($validation->isValidPage($_POST['next_pg']))
|| !($validation->isValidEearliestDate($_POST['earliest_date']))
|| !($validation->isValidLatestDate($_POST['latest_date']))
) {
	$validation->addErrorMsg("An error occured");
	echo json_encode($validation->getResponse());
	die();
}

$json_reply = new stdClass;
$json_reply->comments = array();
$json_reply->paging = array();
$json_reply->data = array();

$db = new PDOFactory();

$topic = new Topic();

$offset = $topic->paging_offset($validation->getPage());

if($validation->getPage() == 1) {
	$sql_topic = sprintf("select * from topic where timestamp >= '%s' order by timestamp desc",$validation->getEearliestDate());
} else {
	$sql_topic = sprintf("select * from topic where timestamp < '%s' order by timestamp desc offset %s limit %s",$validation->getEearliestDate(),$offset,TOPICS_PER_PAGE);
}

try {
	$query_count = $db->get_connection()->query(sprintf("select count(*) from topic where timestamp < '%s'",$validation->getEearliestDate()));
	list($count) = $query_count->fetch();
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

try {
	$query_topic = $db->get_connection()->query($sql_topic);
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

$total_pages = $topic->adjust_paging($count);

while($_topic = $query_topic->fetch(PDO::FETCH_OBJ)) {
	$json_reply->comments[] = sprintf("<div class=\"topic\" id=\"topic_%s\"><a href=\"reply.php?id=%s\"><span class=\"title\">%s</span></a><span class=\"date\">%s</span><span class=\"author_name\">%s</span></div>",
	$_topic->pk_topic_id,$_topic->pk_topic_id,$_topic->title,$topic->format_date($_topic->timestamp),$_topic->author);
}

$pager = new Pager();
$pager->paging($total_pages,$validation->getPage());
$i = $pager->start_page();
$pages = $pager->total_pages();

while($i <= $pages) {
	if ($i==$validation->getPage()) {
		$json_reply->paging[] = "<button type=\"button\" id=\"curr_pg\">$i</button>";
	} else {
		$json_reply->paging[] = "<button class=\"paging_btns\" onClick=\"AjaxTopic.paging_topic(" . $validation->getPage() .",$i,'" . $validation->getEearliestDate() ."');\">$i</button>";
	}
	$i++;
}

$json_reply->data[] = "<div id=\"latest_date\" title='" . $validation->getLatestDate() ."'></div>";
$json_reply->data[] = "<div id='current_pg' title=" .$validation->getPage() ."></div>";

if($query_topic->rowCount() == 0) {
	$arr = array();
	$arr = array("pagefault" => true);
	echo json_encode($arr);
} else {
	echo json_encode($json_reply);
}

unset($db, $pager, $topic,$json_reply);
?>
