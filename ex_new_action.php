<?php
if ($action == 'msgflow') {
	set_time_limit(300);
	$view = isset($_GET['view']) ? $_GET['view'] : 'index';
	$from = isset($_GET['from']) ? (int)$_GET['from'] : time();
	
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json; charset=utf-8');
        	
	if($view == 'index') {
                $fp = fopen($ext_info['path'].'/locks/index_'.$from, "wb+");
                
                if(!flock($fp, LOCK_EX)) {
                    echo "[]";
                    exit;
                }

                // remove all old lock files
                foreach(glob($ext_info['path'].'/locks/index_*') as $old_lock) {
                    $fp_old = fopen($old_lock, "wb+");
                    if(flock($fp_old, LOCK_EX | LOCK_NB)) {
                        unlink($old_lock);
                        flock($fp_old, LOCK_UN);
                    } 
                    fclose($fp_old);
                }

		$query = array(
			'SELECT'	=> 'f.id AS forum_id, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster',
			'FROM'		=> 'categories AS c',
			'JOINS'		=> array(
				array(
					'INNER JOIN'	=> 'forums AS f',
					'ON'			=> 'c.id=f.cat_id'
				),
				array(
					'LEFT JOIN'		=> 'forum_perms AS fp',
					'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
				)
			),
			'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.last_post > '.$from,
			'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
		);
		
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$start_time = time();
		
		while($forum_db->num_rows($result) < 1) {
			sleep(1);
			if((time() - $start_time) > 280) {
				echo "[]";
				exit;
			}
			
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		}

                flock($fp, LOCK_UN);
                fclose($fp);

		$response = array();
		while ($cur_row = $forum_db->fetch_assoc($result))
		{
			$cur_row['date'] = format_time($cur_row['last_post']);
			$cur_row['post_url'] = forum_link($forum_url['post'], $cur_row['last_post_id']);
			$cur_row['forum_newpost_url'] = forum_link($forum_url['search_new_results'], $cur_row['forum_id']);
			$response[] = $cur_row;
		}
	
                echo json_encode($response);
		
	} elseif($view == 'forum') {
		
	}
	
	exit;
}
?>
