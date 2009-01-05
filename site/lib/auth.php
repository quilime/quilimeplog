<?php
    
   /**
    * @param    $dbh            DB      Open database connection
    * @param    $phone_happy    boolean Whether to create tokens suitable for entry on a mobile device
    * @return   array           Token (guaranteed unique) and secret
    */
    function auth_generate_token(&$dbh, $phone_happy = false)
    {
        if($phone_happy) {
            $chars = array('a', 'd', 'g', 'j', 'm', 'p', 't', 'w');
            $length = 8;
            
        } else {
            $chars = array_map('chr', array_merge(range(0x30, 0x39), range(0x41, 0x5A), range(0x61, 0x7A)));
            $length = 32;
        }

        $secret = '';
        
        for($i = 0; $i < $length; $i += 1)
            $secret .= $chars[rand(0, count($chars) - 1)];
            
        while(true)
        {   
            $token = '';
            
            for($i = 0; $i < $length; $i += 1)
                $token .= $chars[rand(0, count($chars) - 1)];
            
            $res = $dbh->query('INSERT INTO tokens (id) VALUES('.$dbh->quoteSmart($token).')');
            
            if(PEAR::isError($res)) {
                continue;
            } else {
                return array($token, $secret);
            }
        }
    }
    
   /**
    * @param    $dbh        DB      Open database connection
    * @param    $token_id   string  Desired token ID
    * @param    $secret     string  Secret for OAuth
    * @param    $type       number  Token type for OAuth
    * @param    $consumer   string  Consumer key for OAuth
    * @param    $username   string  Username from users table
    * @return   boolean     True on success
    */
    function auth_save_token(&$dbh, $token_id, $secret, $type, $consumer, $username)
    {
        $q = sprintf("REPLACE INTO tokens
                      (id, secret, type, consumer_key, user_name, time, deleted)
                      VALUES(%s, %s, %s, %s, %s, NOW(), 0)",
                     $dbh->quoteSmart($token_id),
                     $dbh->quoteSmart($secret),
                     $dbh->quoteSmart($type),
                     $dbh->quoteSmart($consumer),
                     $dbh->quoteSmart($username));
        
        error_log($q);
        
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message.' ('.__FILE__.', '.__LINE__.')');

        return true;
    }
    
    function auth_read_token(&$dbh, $token_id)
    {
        $q = sprintf("SELECT id AS token, secret, type, consumer_key, user_name
                      FROM tokens
                      WHERE id = %s",
                     $dbh->quoteSmart($token_id));
        
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message.' ('.__FILE__.', '.__LINE__.')');

        if($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
            return $row;

        return null;
    }
    
    function auth_read_consumer(&$dbh, $consumer_id)
    {
        $q = sprintf("SELECT id AS `key`, secret, short
                      FROM consumers
                      WHERE id = %s",
                     $dbh->quoteSmart($consumer_id));
        
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message.' ('.__FILE__.', '.__LINE__.')');

        if($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
            return $row;

        return null;
    }
    
   /**
    * @param    $dbh        DB      Open database connection
    * @param    $token_id   string  Desired token ID
    * @return   boolean     True on success
    */
    function auth_delete_token(&$dbh, $token_id)
    {
        $res = $dbh->query('UPDATE tokens SET deleted = 1 WHERE id = '.$dbh->quoteSmart($token_id));
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message.' ('.__FILE__.', '.__LINE__.')');

        return true;
    }
    
    function user_authenticate(&$dbh, $username, $password)
    {
        $q = sprintf("SELECT name FROM users
                      WHERE name = %s
                        AND password = PASSWORD(%s)",
                     $dbh->quoteSmart($username),
                     $dbh->quoteSmart($password));

        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message.' ('.__FILE__.', '.__LINE__.')');
        
        if($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
            return true;

        return false;
    }

?>
