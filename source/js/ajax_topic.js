var AjaxTopic = function() { 
	
$(document).ready(function(){ 
	
	window.onload = function() {
		setInterval(update_topic, CONFIG.TOPIC_UPDATE_INTERVAL);
	};
	
	function update_topic() {
		if(parseInt($("#current_pg").attr("title")) == 1) {
			$.ajax({
				'type' 		: 'POST',
				'url' 		: 'update_topic.php',
				'dataType'  	: 'json',
				'data' 		: {
				'type' 		: 'post',
				'latest_date': $('#latest_date').attr("title")
			},
			'success' 	: function(data){
				if(data.error == undefined) {
					Topic.update_topic(data);
				} 
			},
			'error' 	: function() {
				alert("Topic updates HttpRequest failed");
			}
			});
		}
	}
	
	$('#submit').click(function() {
		$('#error_topic').hide();
		$.ajax({
			'type' 		: 'POST',
			'url' 		: 'post_topic.php',
			'dataType'  	: 'json',
			'data' 		: {
				'type' 	: 'post',
				'author': $('#author').val(),
				'title' : $("#title").val(),
				'msg' 	: $("#msg").val()
			},
			'success' 	: function(data){			
				if(data.error == undefined) {
					Topic.create_topic(data);
				} else {
					Topic.show_topic_error(data);
				}
			},
			'error' 	: function() {
				alert("Topic submission HttpRequest failed");
			}
		});
	});
});

function paging_topic(curr_pg, next_pg, earliest_date) {
	$('#error_topic').hide();
	$.ajax({
		'type' 		: 'POST',
		'url' 		: 'paging_topic.php',
		'dataType'  : 'json',
		'data' 		: {
			'next_pg'	: next_pg,
			'current_pg' 	: curr_pg,
			'earliest_date' : earliest_date,
			'latest_date' 	: $("#latest_date").attr("title")
		},
		'success' 	: function(data){
			Topic.handle_paging(data);
			if(parseInt($("#current_pg").attr("title")) != 1) {
				$("#form").fadeTo(0, 0.4); 
				$("#submit").hide();		  
			} else {
				$("#form").fadeTo(1, 1);
				$("#submit").show();
			}	
		},
		'error' 	: function() {
			alert("Topic paging HttpRequest failed");
		}
	});
}

return {
	paging_topic: paging_topic
};

}();
