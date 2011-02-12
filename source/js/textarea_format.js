var FormatBar = function() { 
	
function topic_display(id) {
	document.write('<div class="format_bar_topic"> \
				<img src="/posts/source/images/bold.png" alt="bold" title="Make text bold" onClick=\'FormatBar.add_tag("[b]","[/b]","' + id + '")\' /> \
				<img src="/posts/source/images/italic.png" alt="italic" title="Make text italic" onClick=\'FormatBar.add_tag("[i]","[/i]","' + id + '")\' /> \
				<img src="/posts/source/images/underline.png" alt="underline" title="Underline text" onClick=\'FormatBar.add_tag("[u]","[/u]","'+ id + '")\' /> \
				<img src="/posts/source/images/link.png" alt="link" title="Add a link" onClick=\'FormatBar.add_url("' + id + '")\' /> \
			</div>');
}

function reply_display(id) {
	return '<div class="format_bar_reply"> \
				<img src="/posts/source/images/bold.png" alt="bold" title="Make text bold" onClick=\'FormatBar.add_tag("[b]","[/b]","' + id + '")\' /><br /> \
				<img src="/posts/source/images/italic.png" alt="italic" title="Make text italic" onClick=\'FormatBar.add_tag("[i]","[/i]","' + id + '")\' /><br /> \
				<img src="/posts/source/images/underline.png" alt="underline" title="Underline text" onClick=\'FormatBar.add_tag("[u]","[/u]","'+ id + '")\' /><br /> \
				<img src="/posts/source/images/link.png" alt="link" title="Add a link" onClick=\'FormatBar.add_url("' + id + '")\' /> \
			</div>';
}

function add_url(id) {
	var textarea = document.getElementById(id);
	var url = window.prompt('URL:', 'http://');

	if (url != null && url != '') {
		if (document.selection) {
			textarea.focus();
			var range = document.selection.createRange();
			range.text = '[url=' + url + ']' + range.text + '[/url]';
		} else {
			var length = textarea.value.length;
			var start = textarea.selectionStart;
			var end = textarea.selectionEnd;
			var url = '[url=' + url + ']' + textarea.value.substring(start, end) + '[/url]';
			textarea.value = textarea.value.substring(0, start) + url
			+ textarea.value.substring(end, length);
		}
	}
}

function add_tag(tag_start, tag_end, id) {
	var textarea = document.getElementById(id);
	if (document.selection) {
		textarea.focus();
		var range = document.selection.createRange();
		range.text = tag_start + range.text + tag_end;
	} else {
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var rep = tag_start + textarea.value.substring(start, end) + tag_end;
		textarea.value = textarea.value.substring(0, start) + rep
		+ textarea.value.substring(end, textarea.value.length);
	}
}

return {
	add_url : add_url,
	add_tag : add_tag,
	topic_display : topic_display,
	reply_display : reply_display
};

}();
