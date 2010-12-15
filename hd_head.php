<?php
if(!defined("MSGFLOW")) exit;
/*
	FORUM_PAGE == 'index'
	FORUM_PAGE == 'viewforum'
	FORUM_PAGE == 'viewtopic'
*/
        $msgflow_query = array(
                'SELECT' => 'posted',
                'FROM'		=> 'posts',
                'ORDER BY'	=> 'posted DESC',
                'LIMIT'         => '1'
        );

	$msgflow_result = $forum_db->query_build($msgflow_query) or error(__FILE__, __LINE__);

        if($forum_db->num_rows($msgflow_result) == 1) {
	    $res = $forum_db->fetch_assoc($msgflow_result);
            $msgflow_timestamp =  $res['posted'];
        } else
            $msgflow_timestamp = '0';


	if(FORUM_PAGE == 'index') {
		$forum_head['jquery'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-1.4.2.min.js"></script>';
		$forum_head['jbeep'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-jbeep-0.1.js"></script>';
		$forum_head['json2'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/json2.js"></script>';
		$forum_head['msgflow'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/msgflow_index.js"></script>';
		$forum_head['msgflow_timestamp'] = '<script type="text/javascript">'.
			'var msgflow_timestamp = '.$msgflow_timestamp.';'.
			'var msgflow_document_title = "";'.
                        'var msgflow_base_url = "'.$base_url.'"'.
                        '</script>';
	}

?>
