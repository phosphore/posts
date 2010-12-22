<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../libs/AutoLoader.php");
Autoloader::register();

use classes\db as db;
use classes\Utils as Utils;
use classes\Validation as Validation;

$validation = new Validation();

if(!(isset($_POST['update_data']))) {
	$validation->addErrorMsg("An error occured");
	echo json_encode($validation->getResponse());
	die();
}

$decoded = json_decode($_POST['update_data']);

if(!($validation->isValidTopicId($decoded->topic_id))
|| !($validation->isValidEearliestDate($decoded->earliest_date))
|| !($validation->isValidMultipleIds($decoded->reply_ids))
) {
	$validation->addErrorMsg("An error occured");
	echo json_encode($validation->getResponse());
	die();
}

$update = true;

if($decoded->current_pg == 1 && $decoded->empty == false) {

	$sql_reply = sprintf("select reply.pk_reply_id, reply.fk_topic_id, reply.pk_reply_id, reply.timestamp, reply.author, reply_to.author AS reply_to, reply.message, reply.parent, reply.fk_reply_id from reply as reply left join reply reply_to on reply.fk_reply_id=reply_to.pk_reply_id where reply.parent IN (select pk_reply_id from reply where fk_reply_id is null and timestamp >= '%s' and fk_topic_id=%s) OR reply.pk_reply_id IN(select pk_reply_id from reply where fk_reply_id is null and timestamp >= '%s' and fk_topic_id=%s) order by reply.position",pg_escape_string($validation->getEearliestDate()),pg_escape_string($validation->getTopicId()),pg_escape_string($validation->getEearliestDate()),pg_escape_string($validation->getTopicId()));

} else if($decoded->current_pg == 1 && $decoded->empty == true) {

	$sql_reply = sprintf("select reply.pk_reply_id, reply.fk_topic_id, reply.pk_reply_id, reply.timestamp, reply.author, reply_to.author AS reply_to, reply.message, reply.parent, reply.fk_reply_id from reply as reply left join reply reply_to on reply.fk_reply_id=reply_to.pk_reply_id where reply.fk_topic_id=%s and reply.timestamp >= '%s' order by reply.position",pg_escape_string($validation->getTopicId()),pg_escape_string($validation->getEearliestDate()));

} else {

	if(count($decoded->reply_ids) == 0) {
		$update = false;
	} else {
		$built_query = Utils::build_query($validation->getMultipleIds());

		$sql_reply = sprintf("select reply.pk_reply_id, reply.fk_topic_id, reply.pk_reply_id, reply.timestamp, reply.author, reply_to.author AS reply_to, reply.message, reply.parent, reply.fk_reply_id from reply as reply left join reply reply_to on reply.fk_reply_id=reply_to.pk_reply_id where reply.fk_topic_id=%s and %s order by reply.position",pg_escape_string($validation->getTopicId()),pg_escape_string($built_query));
	}

}

$reply_update = array();

if($update == true) {
	$db = new db();

	try {
		$result = $db->get_connection()->query($sql_reply);
	} catch (PDOException $e) {
		if(DEBUG) echo $e->getMessage();
		die();
	}

	while($reply = $result->fetch(PDO::FETCH_OBJ)) {
		$type = (empty($reply->fk_reply_id)) ? "reply_to_topic" : "reply_to_reply";
		$reply_update[] = array("id"=> $reply->pk_reply_id, "message" => $reply->message, "type" => $type, "author" => $reply->author, "date" => "NEW", "empty" => $decoded->empty, "reply_to" => "@" . $reply->reply_to);
	}
}

echo json_encode($reply_update);

unset($db);
?>
