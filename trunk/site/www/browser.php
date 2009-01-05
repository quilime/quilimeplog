<?php

    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../lib');
    require_once 'init.php';
    require_once 'data.php';
    require_once 'output.php';

    list($response_format, $response_mime_type) = parse_format($_GET['format'], 'html');
    
    $dbh =& get_db_connection();
    
    set_request_user($dbh);

    if(get_request_user())
    {
        $sm  = get_smarty_instance();
    
        header("Content-Type: {$response_mime_type}; charset=UTF-8");
        print $sm->fetch("browser.{$response_format}.tpl");
    }

?>
