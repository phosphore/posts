<?php
header("Content-Type: application/json; charset=UTF-8");

include_once("../inc/CONFIG.inc.php");
include_once("../class/PDOFactory.class.php");
include_once("../class/Validation.class.php");

$validation = new Validation();

if(!(isset($_POST['latest_date'])) || !($validation->isValidLatestDate($_POST['latest_date']))) {
	$validation->addErrorMsg("An error occured");
	echo json_encode($validation->getResponse());
	die();
}

$db = new PDOFactory();

$sql_topic = sprintf("select pk_topic_id, title, author from topic where timestamp > '%s' order by timestamp",pg_escape_string($validation->getLatestDate()));

try {
	$result = $db->get_connection()->query($sql_topic);
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

$updated_topics = array();
while($topic = $result->fetch(PDO::FETCH_OBJ)) {
	$updated_topics[] = array("id"=> $topic->pk_topic_id, "title" => $topic->title, "author" => $topic->author,"date" => "NEW");
}

echo json_encode($updated_topics);

unset($db,$validation);
?>
