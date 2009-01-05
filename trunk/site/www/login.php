<?php

    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../lib');
    require_once 'init.php';
    require_once 'data.php';
    require_once 'output.php';
    
    list($response_format, $response_mime_type) = parse_format($_GET['format'], 'html');
    
    $posted_action   = $_POST['action'];
    $posted_callback = preg_match('#^\w+://#i', $_POST['callback']) ? $_POST['callback'] : null;
    $posted_username = $_POST['username'];
    $posted_password = $_POST['password'];
    
    /**** Talk to the database ****/
    
    $dbh =& get_db_connection();
    
    set_request_user($dbh);

    $authenticated = array('attempt' => false, 'success' => null);
    
    $reffering_url = 'http://'.get_domain_name().get_base_dir().'/';
    
    $http_host = str_replace('.', '\.', get_domain_name());
    if (preg_match('#^\w+://'.$http_host.'/#i', $_SERVER['HTTP_REFERER'])) {
        if($reffering_url !=  $_SERVER['HTTP_REFERER'])    
            $reffering_url = $_SERVER['HTTP_REFERER'];
    }
    
    switch($posted_action)
    {
        case 'log in':
            
            $user = user_authenticate($dbh, $posted_username, $posted_password);
            $authenticated['attempt'] = true;
            
            if(user_authenticate($dbh, $posted_username, $posted_password)) {
            
                list($token_id, $secret) = auth_generate_token($dbh);
                auth_save_token($dbh, $token_id, null, null, null, $posted_username);
                setcookie(SESSION_COOKIE_NAME, $token_id, 0, get_base_dir().'/', get_domain_name());

                set_request_user($dbh, $posted_username);
                $authenticated['success']  = true;
                $authenticated['callback'] = $posted_callback;

            } else {
                
                set_request_user($dbh, false);
                $authenticated['success'] = false;
            
            }

            break;

        case 'log out':
            
            $token_id = $_COOKIE[SESSION_COOKIE_NAME];
            auth_delete_token($dbh, $token_id);
            setcookie(SESSION_COOKIE_NAME, '', 0, get_base_dir().'/', get_domain_name());
            set_request_user($dbh, false);
            
            break;
    }

    /**** Output and go ****/
    
    $sm = get_smarty_instance();
    $sm->assign('post', array('username' => $posted_username));
    $sm->assign('authenticated', $authenticated);
    $sm->assign('reffering_url', $reffering_url);

    header("Content-Type: {$response_mime_type}; charset=UTF-8");
        print $sm->fetch("login.{$response_format}.tpl");
?>
