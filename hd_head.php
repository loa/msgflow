<?php
/*
	FORUM_PAGE == 'index'
	FORUM_PAGE == 'viewforum'
	FORUM_PAGE == 'viewtopic'
*/
        require_once($ext_info['path'].'/../mobileswitch/mobile_device_detect.php');
        $mob = mobile_device_detect(true,false,true);


	if(FORUM_PAGE == 'index' && !$mob[0]) {
		$forum_head['jquery'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-1.4.2.min.js"></script>';
		$forum_head['jbeep'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/jquery-jbeep-0.1.js"></script>';
		$forum_head['json2'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/json2.js"></script>';
		$forum_head['msgflow'] = '<script type="text/javascript" src="'.$base_url.'/extensions/msgflow/js/msgflow_index.js"></script>';
		$forum_head['msgflow_timestamp'] = '<script type="text/javascript">'.
			'var msgflow_timestamp = '.time().';'.
			'var msgflow_document_title = "";'.
        '</script>';
		
	}

?>
