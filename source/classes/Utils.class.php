<?php
namespace classes;

use DateTime;

final class Utils {

	public static function build_query($reply_ids) {
		$build_query = "";
		foreach($reply_ids as $key => $value) {
			$build_query .= "reply.pk_reply_id=" . $value . " OR ";
		}

		foreach($reply_ids as $key => $value) {
			$build_query .= "reply.parent=" . $value . " OR ";
		}

		$strip_space = strrpos($build_query,"OR");
		return  substr($build_query,0,$strip_space-1);
	}
	
	public static function create_timestamp() {
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$d = new DateTime(date('Y-m-d H:i:s.'.$micro,$t) );
		return $d->format("Y-m-d H:i:s.u");
	}

}

?>
