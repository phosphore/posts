<?php
header("Content-Type: application/json; charset=UTF-8");

require_once("../classes/AutoLoader.php");

$validation = new Validation();
if(!(isset($_POST['topic_id'])) || !(isset($_POST['reply_id'])) || !(isset($_POST['parent_id']))
	|| !(isset($_POST['type'])) || !(isset($_POST['author'])) || !(isset($_POST['comment'])) 
	|| !($validation->isValidTopicId($_POST['topic_id']))
	|| !($validation->isValidReplyId($_POST['reply_id']))
	|| !($validation->isValidParentIdOrNull($_POST['parent_id']))
	|| !($validation->isValidType($_POST['type'])
)) {
	$validation->addErrorMsg("An error has occured");
	echo json_encode($validation->getResponse());
	die();
}

$reply = new Reply($_POST['author'],$_POST['comment']);
if($reply->error()) {
	echo json_encode($reply->getResponse());
	die();
}

$db = new db();
$bbcode = new BBCode;
$bbcode->SetAllowAmpersand(true); 

$error = true;
$reply_to_author = null;

try {
	$db->get_connection()->beginTransaction();
	
	if(strcmp($validation->getType(),"topic") == 0) {
		$sql_post_exists = sprintf("select count(*) from topic where pk_topic_id=%s",$validation->getTopicId());
	} else {
		$sql_post_exists = sprintf("select count(*) from reply where pk_reply_id=%s",$validation->getReplyId());
	}
	list($count) = $db->get_connection()->query($sql_post_exists)->fetch();
	
	if($count != 0) {
		
		$error = false;
		if(strcmp($validation->getType(),"topic") == 0) {
			$sql_update_position = sprintf("update reply set position=position+1 where fk_topic_id=%s",pg_escape_string($validation->getTopicId()));
			$sql_insert_reply = sprintf("INSERT INTO reply (fk_topic_id, fk_reply_id, timestamp, author, message, position, parent) VALUES ('%s',null,localtimestamp,'%s','%s',1,NULL)",$validation->getTopicId(),pg_escape_string($reply->getAuthor()),pg_escape_string($reply->getMessage()));	
		} else {
			$sql_pos_auth = sprintf("select position, author from reply where pk_reply_id=%s",pg_escape_string($validation->getReplyId()));
			list($position,$reply_to_author) = $db->get_connection()->query($sql_pos_auth)->fetch();
			
			$sql_update_position = sprintf("update reply set position=position+1 where position > %s and fk_topic_id=%s",$position,pg_escape_string($validation->getTopicId()));
			$new_position = $position + 1;
			$sql_insert_reply = sprintf("INSERT INTO reply (fk_topic_id, fk_reply_id, timestamp, author, message, position, parent) VALUES (%s,%s,localtimestamp,'%s','%s',%s,%s)",pg_escape_string($validation->getTopicId()),pg_escape_string($validation->getReplyId()), pg_escape_string($reply->getAuthor()),pg_escape_string($reply->getMessage()),$new_position,pg_escape_string($validation->getParentId()));
		}
		
		$db->get_connection()->query($sql_update_position);
		$db->get_connection()->query($sql_insert_reply);
		$id = $db->get_connection()->lastInsertId("reply_pk_reply_id_seq");
		
		$timestamp_query = sprintf("select timestamp from reply where pk_reply_id=%s",$id);
		list($timestamp) = $db->get_connection()->query($timestamp_query)->fetch();
	}
	
	$db->get_connection()->commit();

} catch (PDOException $e) {
	$db->get_connection()->rollBack();
	if(DEBUG) echo $e->getMessage();
	die();
}

if($error == false) {
	echo json_encode(array("id" => $id, "comment" => $bbcode->Parse($reply->getMessage()), "author" => $reply->getAuthor(), "date" => $reply->format_date($timestamp), "reply_to_author" => "@" . $reply_to_author));
} else {
	$validation->addErrorMsg("An error occured");
	echo json_encode($validation->getResponse());
}

unset($db,$reply,$validation,$bbcode);
?>
