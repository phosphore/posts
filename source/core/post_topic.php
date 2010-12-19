<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../libs/AutoLoader.php");
Autoloader::register();

use classes\db as db;
use classes\Validation as Validation;
use classes\Post as Post;
use classes\Topic as Topic;

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

$db = new db();

$sql_topic = sprintf("INSERT INTO topic (author,title,message,timestamp) VALUES ('%s','%s','%s',localtimestamp) RETURNING pk_topic_id, timestamp",pg_escape_string($topic->getAuthor()),pg_escape_string($topic->getTitle()),pg_escape_string($topic->getMessage()));

try {
	list($id, $timestamp) = $db->prepare_and_execute($sql_topic);
} catch (PDOException $e) {
	if(DEBUG) echo $e->getMessage();
	die();
}

echo json_encode(array("id" => $id, "author" => $topic->getAuthor(), "title" => $topic->getTitle(), "date" => $topic->format_date($timestamp)));

unset($db,$topic);
?>