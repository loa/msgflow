<?php
if(!defined("MSGFLOW")) exit;
if ($action == 'msgflow') {
    ini_set('display_errors', '0');

    set_time_limit(300);
    $view = isset($_GET['view']) ? $_GET['view'] : 'index';
    $from = isset($_GET['from']) ? (int)$_GET['from'] : time();
    
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json; charset=utf-8');
            
    $cur_lock = $ext_info['path'].'/'.
        'locks/'.
        'index_'.$forum_user['g_id'].'_'.$from.
        '.lock';

    $fp = fopen($cur_lock, "wb+");
     
    if(!$fp) {
        echo json_encode(array(
            "status"    => "error",
            "console"   => "msgflow couldn't open lock file"
        ));
        exit;
    }

    if(!flock($fp, LOCK_EX)) {
        echo json_encode(array(
            "status"    => "error",
            "console"   => "msgflow couldn't lock the lock file"
        ));
        exit;
    }

    // remove all old lock files
    foreach(glob($ext_info['path'].'/locks/*.lock') as $old_lock) {
        if($old_lock == $cur_lock) continue;
        $fp_old = fopen($old_lock, "wb+");
        if(flock($fp_old, LOCK_EX | LOCK_NB)) {
            unlink($old_lock);
            flock($fp_old, LOCK_UN);
        } 
        fclose($fp_old);
    }

    $query = array(
        'SELECT'=> 'p.id AS post_id, p.poster AS poster, '.
                    'p.posted AS posted, p.message AS message, '.
                    't.id AS topic_id, t.num_views AS num_views, '.
                    't.num_replies AS num_replies, '.
                    'f.id AS forum_id, f.num_topics AS num_topics, '.
                    'f.num_posts AS num_posts',
        'FROM'  => 'posts AS p',
        'JOINS' => array(
            array(
                'INNER JOIN'    => 'topics AS t',
                'ON'            => 't.id = p.topic_id'
            ),
            array(
                'INNER JOIN'    => 'forums AS f',
                'ON'            => 't.forum_id = f.id'
            ),
            array(
                'LEFT JOIN'	=> 'forum_perms AS fp',
                'ON'            => '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
            )
        ),
        'WHERE' => '(fp.read_forum IS NULL OR fp.read_forum = 1) '.
            'AND p.posted > '.$from,
        'ORDER BY' => 't.posted'
    ); 
    
    $result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

    $start_time = time();
    
    while($forum_db->num_rows($result) < 1) {
        sleep(5);
        if((time() - $start_time) > 280) {
            echo json_encode(array(
                "status"    => "success",
                "console"   => ""
            ));
            exit;
        }
            
        $result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
    }

    flock($fp, LOCK_UN);
    fclose($fp);

    $response = array('status' => 'success', 'console' => '');
    $response['updates'] = array();
    while ($cur_row = $forum_db->fetch_assoc($result))
    {
        $cur_row['date'] = format_time($cur_row['posted']);
        $cur_row['post_url'] = forum_link($forum_url['post'], $cur_row['post_id']);
        $cur_row['forum_newpost_url'] = forum_link($forum_url['search_new_results'], $cur_row['forum_id']);
        $response['updates'][] = $cur_row;
    }	
    echo json_encode($response);
    
    exit;
}
?>
