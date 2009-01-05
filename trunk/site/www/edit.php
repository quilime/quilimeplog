<?php

    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../lib');
    require_once 'init.php';
    require_once 'data.php';
    require_once 'output.php';
    
    $params = array();
    $params['action']  = isset($_POST['action'])  ? $_POST['action'] : null;
    $params['id']      = is_numeric($_POST['id']) ? intval($_POST['id']) : null;    
    $params['content'] = isset($_POST['content']) ? $_POST['content'] : null;
    $params['tags']    = isset($_POST['tags'])    ? $_POST['tags'] : null;    
    $params['css']     = isset($_POST['css'])     ? $_POST['css'] : null;
    $params['script']  = isset($_POST['script'])  ? $_POST['script'] : null;
    $params['value']   = isset($_POST['value'])   ? $_POST['value'] : null;    
    $params['type']    = isset($_POST['type'])    ? $_POST['type'] : null;
    $params['hidden']  = isset($_POST['hidden'])  ? $_POST['hidden'] : null;    
    $params['slug']    = isset($_POST['slug'])    ? $_POST['slug'] : null;
	$params['project_slug'] = isset($_POST['project_slug'])    ? $_POST['project_slug'] : null;
    $params['title']   = isset($_POST['title'])   ? $_POST['title'] : null;
    
    $dbh =& get_db_connection();    
    set_request_user($dbh);

    if(get_request_user()) 
    {
        if ($params['action'] == 'insert')  {
            if($params['type'] == 'project')
                $res = insert_new_project($dbh);
			if($params['type'] == 'post')
                $res = insert_new_post($dbh, $params);
            else if($params['type'] == 'section')
                $res = insert_new_section($dbh);
        }
        if ($params['action'] == 'delete') {
            if($params['type'] == 'project')
                $res = delete_project($dbh, $params);
            else if($params['type'] == 'post')
                $res = delete_post($dbh, $params);
        }        
        else if ($params['action'] == 'update') {
            if($params['type'] == 'project')
                $res = update_project($dbh, $params);
            else if($params['type'] == 'post')
                $res = update_post($dbh, $params);
            else if($params['type'] == 'setting')
                $res = update_setting($dbh, $params);
        }
    }
?>
