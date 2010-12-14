$(document).ready(function() {
	// Start long pulling info,
	// setTimeout to get webkit browser to finish loading
	setTimeout("msgflow_pull()", 2000);
});

function msgflow_pull() {
	// Get updates from server
	$.getJSON(msgflow_extern+'?action=msgflow&from=' + msgflow_timestamp, msgflow_callback);
}

function msgflow_callback(data) {
	var last_timestamp = msgflow_timestamp;
	
	// For each forum
	$(data.updates).each(function (index, value) {
		// Check if this post is new
		if(this.last_post > msgflow_timestamp) {
			// Increase the timestamp
			last_timestamp = this.last_post;
			
			// Get the forum_row for faster dom lookups
			var forum_row = $("#forum" + this.forum_id);
			
			// Make the icon change to new posts
			$(forum_row)
				.addClass("new")
				.children("span.icon")
					.addClass("new");
			
			// Update num_topics
			$(forum_row).find("li.info-topics > strong")
				.text(this.num_topics);
			
			// Update num_posts
			$(forum_row).find("li.info-posts > strong")
				.text(this.num_posts);
			
			// Update lastpost date and url
			$(forum_row).find("li.info-lastpost > strong")
				.html('<a href="' + this.post_url + '">' + this.date + '</a>');
			
			// Check if last poster exists (empty forum)
			if($(forum_row).find("li.info-lastpost > cite").length == 0)
				// Add last poster cite
				$("<cite/>").insertAfter($(forum_row).find("li.info-lastpost > strong"));
				
			// Update last poster name
			$(forum_row).find("li.info-lastpost > cite")
				.text('by ' + this.last_poster);
			
			// Add "( New posts)" link on the side of forum title
			if($(forum_row).children("div.item-subject").find("small").length == 0) {
				var newpost = $("<small/>");
				$(newpost)
					.html(' ( <a title="This forum contain posts made since your last visit." ' +
								  'href="' + this.forum_newpost_url + '">' +
								  'New posts</a> )')
					.insertAfter($(forum_row).children("div.item-subject").find("a"));
			}			
		}
	});
	
	// Make sure we got new updates
	if(msgflow_timestamp < last_timestamp) {
		// Set the new timestamp
		msgflow_timestamp = last_timestamp;
		
		// Use jBeep plugin to make a notification beep
		$().jBeep();
		
		// Check if we saved the original document title
		if(!msgflow_document_title)
			msgflow_document_title = document.title;
		
		// Make notification in titlebar
		document.title = 'New posts! - ' + msgflow_document_title;
	}
	// Make new long pull
	msgflow_pull();
}
