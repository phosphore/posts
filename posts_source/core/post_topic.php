<?php
header("Content-Type: application/json; charset=UTF-8");

include_once("../inc/CONFIG.inc.php");
include_once("../class/PDOFactory.class.php");
include_once("../class/Validation.class.php");
include_once("../class/Post.class.php");
include_once("../class/Topic.class.php");

$validation = new Validation();
if(!(isset($_POST['title'])) || !(isset($_POST['author']) || !(isset($_POST['msg'])))) {
	$validation->addErrorMsg("An error has occured");
	echo json_encode($validation->getResponse());
	die();
}

$topic = new Topic($_POST['title'],$_POST['author'],$_POST['msg']);
if($topic->error()) {
	echo json_encode($topic->getResponse());
	die();
}

$db = new PDOFactory();

$sql_topic = sprintf("INSERT INTO topic VALUES (nextval('topic_pk_topic_id_seq'),'%s','%s','%s',localtimestamp) RETURNING pk_topic_id, timestamp",pg_escape_string($topic->getAuthor()),pg_escape_string($topic->getTitle()),pg_escape_string($topic->getMessage()));
try {
	list($id, $timestamp) = $db->prepare_and_execute($sql_topic);
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

echo json_encode(array("id" => $id, "author" => $topic->getAuthor(), "title" => $topic->getTitle(), "date" => $topic->format_date($timestamp)));

unset($db,$topic);
?>
