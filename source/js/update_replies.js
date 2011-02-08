var UpdateReplies = function() {
	
function retrieve_replies(selection) {
	var reply = new Array();
	$(selection).each(function (i,E) {
		reply.push(E);
	});
	return reply;	
}

function pack_update() {
	var replies = UpdateReplies.retrieve_replies(".reply_to_topic");

	var json_reply_ob = new Object;
	json_reply_ob.empty = (replies.length == 0) ? true : false;
	json_reply_ob.topic_id = parseInt($("#topic_id").attr("title"));
	json_reply_ob.current_pg = parseInt($("#current_pg").attr("title"));
	json_reply_ob.earliest_date =  $("#earliest_date").attr("title");
	json_reply_ob.reply_ids = new Array;

	for(var i = 0; i < replies.length; i++) {
		json_reply_ob.reply_ids[i] = Utils.parse_id($(replies[i]).attr("id"));
	}
	
	return JSON.stringify(json_reply_ob);
}

function handle_reply_update(updates) {
	
	mark_reply_removed(updates);

	if(updates.length != 0) { 
		var curr_replies = retrieve_replies(".reply_to_topic,.reply_to_reply");
		if(updates[0].empty == true || curr_replies.length == 0) { 
			sequential_reply_insert(updates); 
		}
		if(updates[0].empty == false) { 
			order_reply(updates);
		}
	} 
	
}

function sequential_reply_insert(updated) {
	for(var i = updated.length - 1; i >= 0; i--) {
		var reply = create_reply(updated,i);
		var tmp_reply = $("#posts").find("#tmp_reply"); 
		if(tmp_reply.length == 0) {
			$(reply).insertAfter($("#posts").children(':first-child'));
		} else {
			$(reply).insertAfter(tmp_reply);
		}
		
		$("#reply_" + updated[i].id).hide();
		$("#reply_" + updated[i].id).fadeIn("slow");
	}
}

function create_reply(updated,i) {

	var type;
	var id;
	var msg;
	var author;
	var date;
	var reply_to;
	if(updated.constructor == Array) {
		type = updated[i].type;
		id = updated[i].id;
		msg = updated[i].message;
		author = updated[i].author;
		date = updated[i].date;
		reply_to = updated[i].reply_to;
	} else {
		type = updated.type;
		id = updated.id;
		msg = updated.message;
		author = updated.author;
		date = updated.date;
		reply_to = updated.reply_to;
	}

	var msg_span = $("<span></span>").attr("class","message").append(msg);
	var author_span = $("<span></span>").attr("class","author_name").append(author);
	
	var date_span;
	if(date == "NEW") {
		date_span = $("<span></span>").attr("class","date_new").append("NEW");
	} else {
		date_span = $("<span></span>").attr("class","date").append(date);
	}
	
	var reply_to_author = $("<span></span>").attr("class","reply_to_author").append(reply_to);
	var reply_button = $("<button></button>").attr("id","replybtn_" + id).attr("class","reply_btn").append("reply");
	
	var reply_div;
	var back_tri_span;
	if(type == "reply_to_topic") { 
		reply_div = $("<div></div>").attr("class","reply_to_topic").attr("id","reply_" + id);
		back_tri_span = $("<span></span>").attr("class","reply_to_topic_tri");
	} else {
		reply_div = $("<div></div>").attr("class","reply_to_reply").attr("id","reply_" + id);
		back_tri_span = $("<span></span>").attr("class","reply_to_reply_tri");
	}
	
	var top_tri_span = $("<span></span>").attr("class","top_tri");
	
	$(reply_div).append(back_tri_span);
	$(reply_div).append(top_tri_span);

	if(type == "reply_to_reply") {
		$(reply_div).append(reply_to_author);
	}

	$(reply_div).append(date_span);
	$(reply_div).append(author_span);
	$(reply_div).append(msg_span);
	$(msg_span).append(reply_button);
	
	$("#replybtn_" + id).live("click",{id: "reply_" + id},Reply.pre_reply);

	add_mouse_event(id);

	return reply_div;
}

function add_mouse_event(id) {
	$('#reply_' + id).live('mouseover mouseout', function(event) {
		if (event.type == 'mouseover') {
			if(Reply.reply_struct.release_reply_button == true) 
				$('#reply_' + id).find(".reply_btn").show();
		} else {
			$('#reply_' + id).find(".reply_btn").hide();
		}
	});
}

function insert_reply_into_pos(updates,curr_reply,option) {
	
	var removed = retrieve_removed_replies(curr_reply);
	var reply = create_reply(updates,null);
	
	if(option == "insert_before" && removed.length == 0) {
		$(reply).insertBefore(curr_reply);
	} 

	if(option == "insert_before" && removed.length > 0) {
		$(reply).insertBefore(removed[removed.length-1]); 
	}

	if(option == "insert_after") {
		var next_reply = $(curr_reply).next();
		if($(next_reply).attr("id") == "tmp_reply") {
			$(reply).insertAfter(next_reply);
		} else {
			$(reply).insertAfter(curr_reply);
		}
	}

	$("#reply_" + updates.id).hide();
	$("#reply_" + updates.id).fadeIn("slow");
	
	add_mouse_event(updates.id);
	
}

function removed_reply_diff(curr_reply, updated) {
	var removed = new Array();
	var match=0;
	
	for(var i=0;i<curr_reply.length;i++) {
		for(var j=0; j<updated.length;j++) {
			if(Utils.parse_id(curr_reply[i].getAttributeNode("id").value) == updated[j].id) 
				match=1;
		}
		
		if(match==0)
			removed.push(curr_reply[i]);
		else 
			match=0;
	}
	return removed;
}

function mark_reply_removed(updates) {
	var curr_replies = retrieve_replies(".reply_to_topic,.reply_to_reply");
	var remove = removed_reply_diff(curr_replies,updates);
	
	if(remove.length != 0) {
		for(var i = 0; i < remove.length; i++) {
			$("#" + $(remove[i]).attr("id")).die();

			if($(remove[i]).is(".reply_to_reply")) {
				remove[i].removeAttribute("class");
				remove[i].removeAttribute("onmouseout");
				remove[i].removeAttribute("onmouseover");
				$(remove[i]).attr("class","removed_reply_to_reply");
				$(remove[i]).children(':first-child').attr("class","reply_to_reply_removed_tri");
			}
			if($(remove[i]).is(".reply_to_topic")) {
				remove[i].removeAttribute("class");
				remove[i].removeAttribute("onmouseout");
				remove[i].removeAttribute("onmouseover");
				$(remove[i]).attr("class","removed_reply_to_topic");
				$(remove[i]).children(':first-child').attr("class","reply_to_topic_removed_tri");
			}

			var next_reply = $(remove[i]).next();
			if($(next_reply).attr("id") == "tmp_reply") {
				$("#post").remove();
				$(next_reply).attr("class","removed_reply_to_reply");
				$(next_reply).children(':first-child').attr("class","reply_to_reply_removed_tri");
			}
		}
	}
	
}

function order_reply(updates) {
	var curr_replies = retrieve_replies(".reply_to_topic,.reply_to_reply"); 
	
	var next = 0;
	var count = 0;

	if(updates.length != 0) {
		for(var i = 0; i < updates.length; i++) {
			if(next == curr_replies.length) {
				if(count == 0) { 
					insert_reply_into_pos(updates[i],prev,"insert_after");
				} else { 
					insert_reply_into_pos(updates[i],last_added,"insert_after");
				}
				last_added = document.getElementById("reply_" + updates[i].id);
				count++;
			} else {
				prev = curr_replies[next]; 
				if(Utils.parse_id(curr_replies[next].getAttributeNode("id").value) == updates[i].id) {
					next++; 
				} else {
					insert_reply_into_pos(updates[i],curr_replies[next],"insert_before");
				}
			}
		}
	} 	

}

function retrieve_removed_replies(startNode) {
	var removed = new Array();
	var node = $(startNode).prev("div");
	
	while(node) {
		if($(node).is(".removed_reply_to_topic") || $(node).is(".removed_reply_to_reply")) {
			removed.push(node);
		} else if($(node).is("#tmp_reply")) {
			if($(node).prev("div").is(".removed_reply_to_reply")) 
				removed.push(node);
		} else {
			break;
		}	
		node = $(node).prev("div");
	}

	return removed;	
}
 
return {
	create_reply: create_reply,
	pack_update: pack_update,
	handle_reply_update: handle_reply_update,
	retrieve_replies: retrieve_replies
};

}();
