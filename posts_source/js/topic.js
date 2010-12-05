var Topic = function() {

function retrieve_curr_topics() {
	var topics = new Array();
	$(".topic").each(function(i, E) {
		topics.push(E);
	});
	return topics;
}
	
function new_topic_diff(curr_topic,updated_topics) {
	var new_topics = new Array();
	var match = 0;
	
	for(var j = 0; j < updated_topics.length; j++) {
		for(var i = 0; i < curr_topic.length; i++) {
			if (Utils.parse_id(curr_topic[i].getAttributeNode("id").value) == updated_topics[j].id) 
				match = 1;
		}
		if(match == 0) { 
			new_topics.push(updated_topics[j]); 
		} else {
			match = 0;
		}
	}
	return new_topics;
}

function update_topic(update) {
	var curr_topic = retrieve_curr_topics();
	var diff = new_topic_diff(curr_topic, update);
	
	for ( var i = 0; i < diff.length; i++) {
		Topic.create_topic(diff, i);
	}
}

function show_topic_error(data) {
	var msg = "<ul>";
	for(var i = 0; i < data.error_msg.length; i++) {
		msg += "<li>" + data.error_msg[i] + "</li>";
	}
	msg += "</ul>";
	$("#error_topic").html(msg).show();
}

function create_topic(response,i) { 
		
	var id;
	var title;
	var author;
	var date;
	if (response.constructor == Array) {
		id = response[i].id;
		title = response[i].title;
		author = response[i].author;
		date = response[i].date;
	} else {
		id = response.id;
		title = response.title;
		author = response.author;
		date = response.date;
	}

	var div_topic = $("<div></div>").attr("id", "topic_" + id).attr("class", "topic");
	var title_span =  $("<span></span>").attr("class", "title").append(title); 
	var author_span = $("<span></span>").attr("class", "author_name").append(author);

	var date_span;
	if (date == "NEW") {
		date_span = $("<span></span>").attr("class","date_new").append("NEW");
	} else {
		date_span = $("<span></span>").attr("class","date").append(date);
	}

	var link = $("<a></a>").attr("href","reply.php?id=" + id);
	$(link).append(title_span);
	$(div_topic).append(link);
	$(div_topic).append(date_span); 
	$(div_topic).append(author_span); 

	$("#posts").prepend($(div_topic));

	if (response.constructor == Array) {
		$("#topic_" + response[i].id).hide();
		$("#topic_" + response[i].id).fadeIn("slow");
	} else {
		$("#topic_" + response.id).hide();
		$("#topic_" + response.id).fadeIn("slow");
	}
	
}
	
function handle_paging(response) {
	var error = false;
	if (response.constructor != Array) {
		error = response.pagefault;
	}
	if (error == true) {
		window.location.href = CONFIG.BASE_URL;
	} else {
		var comments = "";
		for ( var i = 0; i < response.comments.length; i++) {
			comments += response.comments[i];
		}
		$("#posts").html(comments);
		
		var paging = "";
		for ( var j = 0; j < response.paging.length; j++) {
			paging += response.paging[j];
		}
		
		$("#paging").html(paging);
		var data = "";
		for ( var s = 0; s < response.data.length; s++) {
			data += response.data[s];
		}
		$("#data").html(data);
		
	}
}

return {
	handle_paging: handle_paging,
	show_topic_error: show_topic_error,
	create_topic: create_topic,
	retrieve_curr_topics: retrieve_curr_topics,
	update_topic: update_topic
};

}();
