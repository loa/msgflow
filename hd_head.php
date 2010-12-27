<?php
if(!defined("MSGFLOW")) exit;
/*
	FORUM_PAGE == 'index'
	FORUM_PAGE == 'viewforum'
	FORUM_PAGE == 'viewtopic'
*/
    if (FORUM_PAGE == 'index' ||
        FORUM_PAGE == 'viewforum' || 
        FORUM_PAGE == 'searchtopics')
    {

        $msgflow_query = array(
            'SELECT'    => 'posted',
            'FROM'	    => 'posts',
            'ORDER BY'  => 'posted DESC',
            'LIMIT'     => '1'
        );

        $msgflow_result = $forum_db->query_build($msgflow_query) or error(__FILE__, __LINE__);

        if ($forum_db->num_rows($msgflow_result) == 1) {
            $res = $forum_db->fetch_assoc($msgflow_result);
            $msgflow_timestamp =  $res['posted'];
        } else {
            $msgflow_timestamp = '0';
        }

        $msgflow_jsdata = array(
            "var msgflow_timestamp = $msgflow_timestamp;",
            "var msgflow_document_title = \"\";",
            "var msgflow_base_url = \"$base_url\";",
            "var msgflow_forum_page = \"".FORUM_PAGE.'";',
        );

        if (isset($action))
            $msgflow_jsdata['msgflow_forum_page'] = "var msgflow_forum_action = \"$action\";";

        if (FORUM_PAGE == 'viewforum') {
            $msgflow_jsdata[] = "var msgflow_forum_id = $id;";
        }
        
        if (FORUM_PAGE == 'viewforum'
            || FORUM_PAGE == 'searchtopics') {
            $msgflow_jsdata[] = "var msgflow_disp_topics = ".$forum_config['o_disp_topics_default'].";";
        }

        $forum_head['jquery'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-1.4.2.min.js"></script>';
        //$forum_head['json2'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/json2.js"></script>';
        //$forum_head['msgflow'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/msgflow.js"></script>';
        $forum_head['msgflow'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/msgflow_min.js"></script>';
        $forum_head['msgflow_jsdata'] = 
            "<script type=\"text/javascript\">\n\t".
            implode($msgflow_jsdata, "\n\t").
            "\n</script>";
    }

?>
