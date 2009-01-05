<?php
    
    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../lib');
    require_once 'init.php';
    require_once 'data.php';
    require_once 'output.php';
    
    list($response_format, $response_mime_type) = parse_format($_GET['format'], 'html');
    
    $params = array();
    $params['type'] = isset($_GET['type']) ? $_GET['type'] : null;
    $params['slug'] = isset($_GET['slug']) ? $_GET['slug'] : null;

    if(!$params['type'])    $template = 'upload';
    else                    $template = "upload-".$params['type'];

    $dbh =& get_db_connection();
    set_request_user($dbh);
    
    if(get_request_user())
    {
        if($params['type'] == 'thumb')
        {
            if(
                sizeof($_FILES) > 0 /* && 
                $_FILES['uploadedfile']['error'] == UPLOAD_ERR_OK &&
                (
                    $_FILES['uploadedfile']['type'] == 'image/jpg' ||
                    $_FILES['uploadedfile']['type'] == 'image/jpeg' ||
                    $_FILES['uploadedfile']['type'] == 'image/png'
                )*/
            )
            {
                $src  = $_FILES['uploadedfile']['name'];
                $dest = $params['slug'] . '-thumb.png';
                $target_path = $_SERVER['DOCUMENT_ROOT'] . get_base_dir() . '/thumbs/';

                move_uploaded_file($_FILES['uploadedfile']['tmp_name'], "/tmp/$src");

                // $convert = 'convert -thumbnail 160x160 -bordercolor transparent -border 160 -crop 160x160+0+0 +repage -gravity center "' . "/tmp/" . $src . '" "' . $target_path . $dest. '"'; // fit inside 160x160 sq
                $convert = 'convert -thumbnail -resize x160 -resize \'160x<\' -resize 160 "' . "/tmp/" . $src . '" "' . $target_path . $dest. '"'; // fit inside 160x160 sq                
                exec ( $convert );
            }
        }
    }
    $dbh->disconnect();


    // TEMPLATE
    $sm  = get_smarty_instance();
    
    if(get_request_user())
    {
        $sm->assign('type', $params['type']);
        $sm->assign('slug', $params['slug']);
    }

    // OUTPUT
    header("Content-Type: {$response_mime_type}; charset=UTF-8");
    print $sm->fetch($template.".{$response_format}.tpl");
?>
