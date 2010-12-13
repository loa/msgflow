<?php
if(!defined("MSGFLOW")) exit;
/*
	FORUM_PAGE == 'index'
	FORUM_PAGE == 'viewforum'
	FORUM_PAGE == 'viewtopic'
*/
        $query = array(
                'SELECT' => 'posted',
                'FROM'		=> 'posts',
                'ORDER BY'	=> 'posted DESC',
                'LIMIT'         => '1'
        );

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

        if($forum_db->num_rows($result) == 1) {
	    $res = $forum_db->fetch_assoc($result);
            $timestamp =  $res['posted'];
        } else
            $timestamp = 'fail';


	if(FORUM_PAGE == 'index') {
		$forum_head['jquery'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-1.4.2.min.js"></script>';
		$forum_head['jbeep'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-jbeep-0.1.js"></script>';
		$forum_head['json2'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/json2.js"></script>';
		$forum_head['msgflow'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/msgflow_index.js"></script>';
		$forum_head['msgflow_timestamp'] = '<script type="text/javascript">'.
			'var msgflow_timestamp = '.$timestamp.';'.
			'var msgflow_document_title = "";'.
                        'var msgflow_extern = "'.$base_url.'/extern.php"'.
                        '</script>';
	}

?>
