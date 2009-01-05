<?php

    putenv("TZ=America/Los_Angeles");
    ini_set('include_path', ini_get('include_path')  . PATH_SEPARATOR . '/<absolute path to lib>/lib');
    ini_set('include_path', ini_get( 'include_path') . PATH_SEPARATOR . "/<absolute path to PEAR>/lib/PEAR" );
    ini_set('include_path', ini_get( 'include_path') . PATH_SEPARATOR . "/<absolute pat hto PEAR php>/php" );
    
    define('TMP_DIR', dirname(realpath(__FILE__)).'/../tmp');
    
    define('DB_DSN', "mysql://<user>:<password>@<mysql url>/<mysql db>");

    define('SITE_TITLE', 'project log');

    define('SESSION_COOKIE_NAME', 'plog-session-id');
    session_name('plog-session-id');
    
?>
