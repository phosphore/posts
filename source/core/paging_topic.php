<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../classes/AutoLoader.php");

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

$db = new db();

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

$topic_xml = new PostXML();
 
while($_topic = $query_topic->fetch(PDO::FETCH_OBJ)) {
		$topic_xml->build_post_xml($_topic->pk_topic_id, $_topic->title,
		$topic->format_date($_topic->timestamp), $_topic->author);
}

$json_reply->comments = $topic_xml->transform();

$pager = new Pager();
$pager->paging($total_pages,$validation->getPage());

$xml_pager = new TopicXMLPager();
$xml_pager->build_pager($pager->start_page(),$validation->getPage(), $pager->total_pages(), $validation->getEearliestDate());
$json_reply->paging= $xml_pager->transform_pager();

$data = new TopicXMLData();
$data->build_data($validation->getLatestDate(), $validation->getPage());
$json_reply->data = $data->transform_data();

if($query_topic->rowCount() == 0) {
	$arr = array();
	$arr = array("pagefault" => true);
	echo json_encode($arr);
} else {
	echo json_encode($json_reply);
}

unset($db,$pager,$topic,$json_reply);
?>
