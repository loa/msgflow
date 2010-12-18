$(document).ready(function() {
    // Start long pulling info,
    // setTimeout to get webkit browser to finish loading
    setTimeout("msgflow_pull()", 2000);
});

function msgflow_pull() {
    // Get updates for index page
    $.getJSON(msgflow_base_url+'/extern.php?action=msgflow&from=' + msgflow_timestamp, msgflow_callback);
}

function msgflow_beep() {
    var audioElement = document.createElement('audio');
    
    // Check if browser supports audio tag
    if(typeof audioElement.load == 'function') {
        // Load audo and play
        audioElement.setAttribute('src', msgflow_base_url+"/extensions/msgflow/sound/beep_mult.ogg");
        audioElement.load();
        audioElement.play();
    }
}

function msgflow_callback(data) {
    var last_timestamp = msgflow_timestamp;

    // For each update
    $(data.updates).each(function () {
        // Check if this post is new
        if(this.posted > msgflow_timestamp) {
            // Increase the timestamp
            last_timestamp = this.posted;

            // Update index
            if(msgflow_forum_page == 'index') {
                msgflow_index(this);
            }

            // Update viewforum
            else if(msgflow_forum_page == 'viewforum') {
                if(msgflow_forum_id == this.forum_id)
                    msgflow_viewforum(this);
            }
 
        }
    });
    
    // Make sure we got new updates
    if(msgflow_timestamp < last_timestamp) {
            // Set the new timestamp
            msgflow_timestamp = last_timestamp;
            
            // Sound notification
            msgflow_beep();
 
            // Check if we saved the original document title
            if(!msgflow_document_title)
                    msgflow_document_title = document.title;
            
            // Make notification in titlebar
            document.title = 'New posts! - ' + msgflow_document_title;
    }
    // Make new long pull
    if(data.status == "success")
            msgflow_pull();
}

function msgflow_index(data) {
    // Get the forum_row for faster dom lookups
    var forum_row = $("#forum" + data.forum_id);
    
    // Make the icon change to new posts
    $(forum_row)
        .addClass("new")
        .children("span.icon")
        .addClass("new");
    
    // Update num_topics
    $(forum_row).find("li.info-topics > strong")
        .text(data.num_topics);
    
    // Update num_posts
    $(forum_row).find("li.info-posts > strong")
        .text(data.num_posts);
    
    // Update lastpost date and url
    $(forum_row).find("li.info-lastpost > strong")
        .html('<a href="' + data.post_url + '">' + data.date + '</a>');
    
    // Check if last poster exists (empty forum)
    if($(forum_row).find("li.info-lastpost > cite").length == 0)
        // Add last poster cite
        $("<cite/>").insertAfter($(forum_row).find("li.info-lastpost > strong"));
            
    // Update last poster name
    $(forum_row).find("li.info-lastpost > cite")
        .text('by ' + data.poster);
    
    // Add "( New posts)" link on the side of forum title
    if($(forum_row).children("div.item-subject").find("small").length == 0) {
        var newpost = $("<small/>");
        $(newpost)
            .html(' ( <a title="This forum contain posts made since your last visit." ' +
                'href="' + data.forum_newpost_url + '">' +
                'New posts</a> )')
            .insertAfter($(forum_row).children("div.item-subject").find("a"));
    }			
}

function msgflow_viewforum(data) {
    // Check if topic already is visible
    if ($("#topic"+data.topic_id).length > 0) {
        // Get the forum_row for faster dom lookups
        var topic_row = $("#topic" + data.topic_id);
       
        // Make the icon change to new posts
        $(topic_row)
            .addClass("new")
            .children("span.icon")
            .addClass("new");

        // Update num_replies
        $(topic_row).find("li.info-replies > strong")
            .text(data.num_replies);

        // Update num_views
        $(topic_row).find("li.info-views > strong")
            .text(data.num_views);        

        // Update lastpost date and url
        $(topic_row).find("li.info-lastpost > strong")
            .html('<a href="' + data.post_url + '">' + data.date + '</a>');
    
        // Update last poster name
        $(topic_row).find("li.info-lastpost > cite")
            .text('by ' + data.poster);

        // Add "( New posts )" link on the side of the topic creators name
        if ($(topic_row).children("div.item-subject").find("span.item-nav").length == 0) {
            var newpost = $("<span/>");
            $(newpost)
                .addClass("item-nav")
                .html(
                    ' ( <em class="item-newposts">' +
                    ' <a href="' + data.topic_newpost_url + '">New posts</a>' +
                    '</em> )'
                )
                .insertAfter($(topic_row).find("span.item-starter"));
        } 

        // Check if topic is sticky
        if ($(topic_row).hasClass("sticky")) {
            // Remove class main-first-item from top_row
            $("div.main-item:first").removeClass("main-first-item");
            
            // Add main-first-item and add move to top
            $(topic_row)
                .addClass("main-first-item")
                .prependTo($("div.main-content"));
        }
        
        // Topic is not sticky
        else {
            // Get the topic beneath stickies as rel_row
            var rel_row = $("div.main-item").not(".sticky").filter(":first");
            
            // Make sure we found some other topic
            if($(rel_row).length > 0 && $(rel_row)[0] != $(topic_row)[0]) {
                // Remove main-first-item class from rel_row
                $(rel_row).removeClass("main-first-item");
     
                // Insert topic_row before rel_row
                $(topic_row).insertBefore($(rel_row));
            } 

            // Add class main-first-item to top_row
            $("div.main-item:first").addClass("main-first-item");
        } 
    }
   
    // Topic is not visible, create new row
    else {
        // Generate a new row
        var topic_row = $("<div/>")
            .attr("id", "topic"+data.topic_id)
            .addClass("main-item normal new")
            .append(
                $("<span/>")
                    .addClass("icon normal new")
                    .html("<!-- -->")
            )
            .append(
                $("<div/>")
                    .addClass("item-subject")
                    .append(
                        $("<h3/>")
                            .addClass("hn")
                            .append(
                                $("<span/>").addClass("item-num")
                            )
                            .append(
                                $("<a/>")
                                    .attr("href", data.topic_url)
                                    .text(data.subject)
                            )
                    )
                    .append(
                        $("<p/>").append(
                            $("<span/>").addClass("item-starter").text("by ")
                                .append(
                                    $("<cite/>").text(data.creator)
                                )
                        )
                        .append(
                            $("<span/>").addClass("item-nav").text(" ( ").append(
                                $("<em/>").addClass("item-newposts").append(
                                    $("<a/>")
                                        .attr("href", data.topic_newpost_url)
                                        .text("New posts")
                                )
                            ).append(" )")
                        )
                    )
            )
            .append(
                $("<ul/>")
                    .addClass("item-info")
                    .append(
                        $("<li/>").addClass("info-replies").append(
                            $("<strong/>").text(data.num_replies)
                        )
                        .append($("<span/>").addClass("label").text(" reply"))
                    )
                    .append(
                        $("<li/>").addClass("info-views").append(
                            $("<strong/>").text(data.num_views)
                        )
                        .append($("<span/>").addClass("label").text(" views"))
                    )
                    .append(
                        $("<li/>").addClass("info-lastpost").append(
                            $("<span/>").addClass("label").text("Last post ")
                        )
                        .append(
                            $("<strong/>").append(
                                $("<a/>").attr("href", data.post_url).text(data.date)
                            )
                        )
                        .append(
                            $("<cite/>").text("by ").append(data.poster)
                        )
                    )
            );

        // Check if there are more than allowed topics on the page visible
        if ($("div.main-item").length > msgflow_disp_topics) {
            // Remove the topic in the bottom of the page
            $("div.main-item:last").remove();
        }
        
        // Get the topic beneath stickies as rel_row
        var rel_row = $("div.main-item").not(".sticky").filter(":first");
        
        // Make sure we got a none sticky
        if($(rel_row).length > 0) {
            // Remove main-first-item class from rel_row
            $(rel_row).removeClass("main-first-item");
            
            // Insert topic_row before rel_row
            $(topic_row).insertBefore($(rel_row));
        }
        
        // There exists only stickies
        else {
            // Take the last sticky
            rel_row = $("div.main-item:last");

            // Insert after the last sticky
            $(topic_row).insertAfter($(rel_row));
        }
        
        // Add class main-first-item to top_row
        $("div.main-item:first").addClass("main-first-item");
    }

    // Itterate through all div.main-item
    $("div.main-item").each(function (index) {
        // Remove classes even and odd from all div.main-item
        $(this)
            .removeClass("even")
            .removeClass("odd");

        // Set classes even and odd to corrent div.main-item
        if(index%2 == 0)
            $(this).addClass("odd");
        else
            $(this).addClass("even");

        // Set span.item-num
        $(this).find("span.item-num").text(index+1);
    });
}
