var Reply = function() {

var reply_struct = {
		comment : null,
		topic_id: null,
		reply_id: null,
		type: null,
		div : null,
		release_reply_button : true,
		parent_id: null,
		author : null
};

$('.input_name').live('focus', function() {
	if($(".input_name").attr("value") == ("(enter your name)")) {
		$(".input_name").attr("value","");
	}
});

$('.reply_text_area').live('focus', function() {
	if($(".reply_text_area").attr("value") == ("(enter your message)")) {
		$(".reply_text_area").attr("value","");
	}
});

$('#cancel').live('click', function() {
	$(reply_struct.div).remove();
	reply_struct.release_reply_button = true; 
});

function handle_post_reply(response) {
	$(reply_struct.div).empty();
	$(reply_struct.div).attr("id","reply_" + response.id);

	var msg = $("<span></span>").attr("class","message").append(response.comment);
	var author = $("<span></span>").attr("class","author_name").append(response.author);	
	var date = $("<span></span>").attr("class","date").append(response.date);	
	var reply_to_author = $("<span></span>").attr("class","reply_to_author").append(response.reply_to_author);	
	var reply_button = $("<button></button>").attr("id","replybtn_" + response.id).addClass("reply_btn").append("reply");		
	
	var back_tri_span;
	if (reply_struct.type == "topic") { 
		$(reply_struct.div).attr("class", "reply_to_topic");
		back_tri_span = $("<span></span>").attr("class","reply_to_topic_tri");
	} else {
		$(reply_struct.div).attr("class","reply_to_reply");
		back_tri_span = $("<span></span>").attr("class","reply_to_reply_tri");
	}
	
	var top_tri_span = $("<span></span>").attr("class","top_tri");
	
	$(reply_struct.div).append(back_tri_span);
	$(reply_struct.div).append(top_tri_span);

	if (reply_struct.type == "reply_to_reply" || reply_struct.type == "reply_to_topic") {
		$(reply_struct.div).append(reply_to_author);
	}

	$(reply_struct.div).append(date);
	$(reply_struct.div).append(author);
	$(reply_struct.div).append(msg);
	$(msg).append(reply_button);
	
	$("#replybtn_" + response.id).live("click",{id: "reply_" + response.id},Reply.pre_reply);
	
	$('#reply_' + response.id).live('mouseover mouseout', function(event) {
		  if (event.type == 'mouseover') {
			  if(reply_struct.release_reply_button == true) {
				  $('#reply_' + response.id).find(".reply_btn").show();
			  }
		  } else {
			  $('#reply_' + response.id).find(".reply_btn").hide();
		  }
	});
	
	reply_struct.release_reply_button = true;	
}

function show_reply_error(data) {
	var right_arrow = $("<div></div>").attr("class","right-arrow");
	var error = $("<div></div>").attr("id","error_reply");
	
	var msg = "<ul>";
	
	for(var i = 0; i < data.error_msg.length; i++) {
		msg += "<li>" + data.error_msg[i] + "</li>";
	}
	msg += "</ul>";
	
	$(reply_struct.div).append(error);
	$("#error_reply").html(msg).show();
	$(error).append(right_arrow);
}

function pre_reply(id) {
	var reply_id = (id.data != null) ? id.data.id : id;		
	
	reply_struct.parent_id_reply = null;
	
	Reply.hide_button(reply_id);
	reply_struct.release_reply_button = false;

	reply_struct.reply_id = Utils.parse_id(reply_id);
	reply_struct.type = $("#" + reply_id).attr("class");
	
	var back_tri_span;
	if (reply_struct.type == "topic") {  
		topic_reply = $("<div></div>").attr("id","tmp_reply").attr("class","tmp_reply_to_topic");
		back_tri_span = $("<span></span>").attr("class","reply_to_topic_tri");
	} else {
		topic_reply = $("<div></div>").attr("id","tmp_reply").attr("class","tmp_reply_to_reply");
		back_tri_span = $("<span></span>").attr("class","reply_to_reply_tri");
	}

	var top_tri_span = $("<span></span>").attr("class","top_tri");
	
	var input_name = $("<input />").attr("class","input_name").attr("value","(enter your name)");
	var txt_area = $("<textarea></textarea>").attr("class","reply_text_area").attr("rows","5").attr("id","msg").append("(enter your message)");
	var post_and_canel_span = $("<span></span>").attr("class","post_and_cancel");
	var post_btn = $("<button></button>").attr("id","post").append("post");
	var cancel_btn = $("<button></button>").attr("id","cancel").append("cancel");
	
	$(topic_reply).append(FormatBar.reply_display('msg'));
	$(topic_reply).append(back_tri_span);
	$(topic_reply).append(top_tri_span);

	$(topic_reply).append(txt_area);
	$(topic_reply).append(input_name); 
	$(post_and_canel_span).append(post_btn);
	$(post_and_canel_span).append(cancel_btn);
	$(topic_reply).append(post_and_canel_span);
	
	$(topic_reply).insertAfter("#" + reply_id);
	
	reply_struct.topic_id = Utils.parse_id($(".topic").attr("id"));

	var parent = $(topic_reply).prevAll(".reply_to_topic").attr("id");
	if (parent != null) {
		reply_struct.parent_id = Utils.parse_id(parent);
	} 

	reply_struct.div = topic_reply.get(0);
	reply_struct.comment = txt_area;
	reply_struct.author = input_name;
}

function show_button(id) {
	if (reply_struct.release_reply_button == true) { 
		$("#" + id).find(".reply_btn").show();
	} 
}

function hide_button(id) {
	$(".reply_btn").each(function(i, E) {
		$(E).hide();
	});
}

function reset() {
	reply_struct.release_reply_button = true;
}

function handle_paging_reply(response) {
	var error = false;
	var id;
	if (response.constructor != Array) {
		error = response.pagefault;
		id = response.id;
	}
	if (error == true) {
		window.location.href = CONFIG.REPLY_PAGE + id;
	} else {
		$("#posts").html(response.comments);

		$("#paging").html(response.paging);

		$("#data").html(response.data);
	}
}


return {
	handle_paging_reply: handle_paging_reply,
	reset: reset,
	hide_button: hide_button,
	show_button: show_button,
	pre_reply: pre_reply,
	show_reply_error: show_reply_error,
	handle_post_reply: handle_post_reply,
	reply_struct: reply_struct
};

}();
