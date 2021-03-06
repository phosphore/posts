var AjaxReply = function() { 
	
$(document).ready(function() { 
	
	$(window).load(function() {
		$("#content").show();
		setInterval(update, CONFIG.REPLY_UPDATE_INTERVAL);  
		Reply.reply_struct.okay= true;
	});
	
	$('#post').live('click', function() {
		$.ajax({
		  'type': "POST",
			'url' 		: '/posts/source/core/post_reply.php',
			'dataType'  : 'json',
			'data' 		: {
			'author'	: $(Reply.reply_struct.author).val(),
			'comment' 	: $(Reply.reply_struct.comment).val(),
			'reply_id' 	: Reply.reply_struct.reply_id,
			'type' 		: Reply.reply_struct.type,
			'topic_id' 	: Reply.reply_struct.topic_id,
			'parent_id' : Reply.reply_struct.parent_id
		},
		'success' 	: function(data){
			if(data.error == undefined) {
				Reply.handle_post_reply(data);
			} else {
				Reply.show_reply_error(data);
			}
		},
		'error' 	: function() {
			alert("Reply posting HttpRequest failed");
		}
		});
	});
	
});

function update() { 
	$.ajax({
		'type' 		: 'POST',
		'url' 		: '/posts/source/core/update_reply.php',
		'dataType'  	: 'json',
		'data' 		: {
		'update_data': UpdateReplies.pack_update()
	},
	'success' 	: function(data){
		UpdateReplies.handle_reply_update(data);
	},
	'error' 	: function() {
		alert("Update HttpRequest failed");
	}
	});
}


function paging_reply(topic_id, current_page, next_page, earliest_date) {
	$.ajax({
		'type' 		: 'POST',
		'url' 		: '/posts/source/core/paging_reply.php',
		'dataType'  	: 'json',
		'data' 		: {
			'current_pg'	: current_page,
			'next_page' 	: next_page,
			'earliest_date' : earliest_date,
			'topic_id' 	: topic_id
		},
		'success' 	: function(data){
			Reply.handle_paging_reply(data);
		},
		'error' 	: function() {
			alert("Reply paging HttpRequest failed");
		}
	});
}

return {
	paging_reply: paging_reply
};

}();
