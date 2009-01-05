<?php

    define('OAUTH_SIGNATURE_PROVIDED', 1);      // oauth signature provided
    define('OAUTH_SIGNATURE_MISSING', 2);       // oauth signature missing
    define('OAUTH_SIGNATURE_BAD_METHOD', 3);    // oauth signature with unsupported signing method
    
    define('OAUTH_TOKEN_UNAUTHORIZED', 0);      // unauthorized token type
    define('OAUTH_TOKEN_AUTHORIZED', 1);        // authorized token type
    define('OAUTH_TOKEN_ACCESS', 2);            // access token type
    
    require_once 'output.php';
    require_once 'HTTP/Request/OAuth.php';

   /**
    * Checks whether a request signature has been provided.
    *
    * @return   int One of the OAUTH_* constants to communicate signature status.
    */
    function oauth_signature_provided()
    {
        // at least these must be present for every request
        $minimal_parameter_names = array('consumer_key', 'timestamp', 'nonce',
                                         'signature', 'signature_method');

        $signature_provided = true;
        
        $oauth_parameters = oauth_parameters();
        
        foreach($minimal_parameter_names as $name)
            if(!isset($oauth_parameters[$name]))
                $signature_provided = false;
        
        if(!$signature_provided)
            return OAUTH_SIGNATURE_MISSING;
            
        if(strtolower($oauth_parameters['signature_method']) != 'md5')
            return OAUTH_SIGNATURE_BAD_METHOD;
        
        return OAUTH_SIGNATURE_PROVIDED;
    }

   /**
    * Compares expected request signature to provided signature.
    *
    * @param    string  $consumer_secret    OAuth consumer secret
    * @param    string  $token_secret       OAuth token secret
    * @return   boolean True if the signature checks out, false otherwise.
    */
    function oauth_verify_signature($consumer_secret, $token_secret)
    {
        return oauth_provided_signature() == oauth_expected_signature($consumer_secret, $token_secret);
    }
    
   /**
    * Internal: the expected OAuth signature.
    *
    * @param    string  $consumer_secret    OAuth consumer secret
    * @param    string  $token_secret       OAuth token secret
    * @return   string  Expected OAuth signature for this request
    */
    function oauth_expected_signature($consumer_secret, $token_secret)
    {
        $signature_parts = array($_SERVER['REQUEST_METHOD'],
                                 oauth_request_url(),
                                 oauth_parameters_to_string(),
                                 $consumer_secret,
                                 $token_secret);

        $signed_string = join('&', array_map('rawurlencode', $signature_parts));
        
        $oauth_parameters = oauth_parameters();
        
        switch(strtolower($oauth_parameters['signature_method']))
        {
            case 'md5':
                // only md5 is supported
                $expected_signature = HTTP_Request_OAuth::_md5($signed_string);
                break;
                
            default:
                $expected_signature = '';
                break;
        }
        
        return $expected_signature;
    }
    
   /**
    * Internal: the provided OAuth signature.
    *
    * @return   string  Actual OAuth signature for this request
    */
    function oauth_provided_signature()
    {
        $oauth = oauth_parameters();
        
        return $oauth['signature'];
    }
    
   /**
    * Internal: parsed Authorization request header.
    *
    * @return   array   Associative array of all "oauth_*" parameters
    *                   in an "Authorization: Oauth ..." header.
    */
    function parse_authorization_header()
    {
        $parts = array();
        
        if(function_exists('getallheaders'))
            foreach(getallheaders() as $name => $value)
                if(strtolower($name) == 'authorization')
                    if(preg_match('/^OAuth realm="([^"]*)"((,\s*oauth_\w+="[^"]*")+)\s*$/Uis', $value, $matches))
                    {
                        if(preg_match_all('/,\s*(oauth_\w+)="([^"]*)"/Uis', $matches[2], $matches, PREG_SET_ORDER))
                            foreach($matches as $match)
                                $parts[$match[1]] = $match[2];
                    }
        
        return $parts;
    }
    
   /**
    * Internal: OAuth parameters passed in the request.
    *
    * @return   array   Associative array OAuth parameters from the current request,
    *                   with the "oauth_" part removed from the name where applicable.
    */
    function oauth_parameters()
    {
        $parameter_names = array('oauth_token', 'oauth_token_secret',
                                 'oauth_consumer_key', 'oauth_consumer_secret',
                                 'oauth_signature', 'oauth_signature_method',
                                 'oauth_timestamp', 'oauth_nonce',
                                 'oauth_version');
    
        $oauth = array();
        
        foreach($_GET as $key => $value)
            if(in_array($key, $parameter_names))
                $oauth[preg_replace('/^oauth_/', '', $key)] = $value;
        
        foreach($_POST as $key => $value)
            if(in_array($key, $parameter_names))
                $oauth[preg_replace('/^oauth_/', '', $key)] = $value;
        
        foreach(parse_authorization_header() as $key => $value)
            if(in_array($key, $parameter_names))
                $oauth[preg_replace('/^oauth_/', '', $key)] = $value;

        return $oauth;
    }
    
   /**
    * Internal: the request URL.
    *
    * @return   string  Current request URL, as the client should have provided it.
    */
    function oauth_request_url()
    {
        $hostname = $_SERVER['HTTP_HOST'];

        foreach(getallheaders() as $name => $value)
            if(strtolower($name) == 'host')
                $hostname = $value;

        return 'http://'.$hostname.$_SERVER['PHP_SELF'];
    }
    
   /**
    * Internal: normalized request parameters, in a string for signing.
    *
    * @return   string  String of request parameters expected signature.
    */
    function oauth_parameters_to_string()
    {
        $headers = getallheaders();
        
        if(in_array($headers['content-type'], array('application/x-www-form-urlencoded', 'multipart/form-data'))) {
            $params = array_merge($_GET, $_POST, parse_authorization_header());

        } else {
            $params = array_merge($_GET, parse_authorization_header());
        }
        
        // never, never.
        unset($params['oauth_signature']);
        
        $keys = array_keys($params);
        $values = array_values($params);
        
        // sort by name, then by value
        array_multisort($keys, SORT_ASC, $values, SORT_ASC);
        
        // pack parameters into a normalized string
        $normalized_keyvalues = array();
        
        for($i = 0; $i < count($keys); $i += 1)
            $normalized_keyvalues[] = rawurlencode($keys[$i]).'='.rawurlencode($values[$i]);
        
        return join('&', $normalized_keyvalues);
    }
    


    //======== Below here are local storage function for token info, not generally applicable.
    
   /**
    * True if the signatue is good, false otherwise.
    *
    * @param    string  $consumer_secret    OAuth consumer secret
    * @param    string  $token_secret       OAuth token secret
    */
    function oauth_valid_signature($consumer_secret, $token_secret)
    {
        switch(oauth_signature_provided())
        {
            case OAUTH_SIGNATURE_MISSING:
                return false;
    
            case OAUTH_SIGNATURE_BAD_METHOD:
                return false;
                
            case OAUTH_SIGNATURE_PROVIDED:
                if(!oauth_verify_signature($consumer_secret, $token_secret))
                    return false;
        }

        return true;
    }
    
   /**
    * No return value, just dies if the signature fails.
    *
    * @param    string  $consumer_secret    OAuth consumer secret
    * @param    string  $token_secret       OAuth token secret
    */
    function oauth_enforce_signature_or_die($consumer_secret, $token_secret)
    {
        switch(oauth_signature_provided())
        {
            case OAUTH_SIGNATURE_MISSING:
                die_with_code(400, 'Sorry, you don\'t seem to have provided an OAuth signature.');
                break;
    
            case OAUTH_SIGNATURE_BAD_METHOD:
                die_with_code(400, 'Sorry, you need to sign your OAuth signature with method md5.');
                break;
                
            case OAUTH_SIGNATURE_PROVIDED:
                if(!oauth_verify_signature($consumer_secret, $token_secret))
                    die_with_code(401, 'Sorry, your OAuth signature doesn\'t check out. We were expecting "'.oauth_expected_signature($consumer_secret, $token_secret).'" but got "'.oauth_provided_signature().'".');
    
                break;
        }
    }
    
    function oauth_generate_token()
    {
        return uniqid('');
    
        /*
        $filename = tempnam(TMP_DIR, 'oauth.');
        chmod($filename, 0666);
        return substr($filename, strlen(TMP_DIR.'/oauth.'));
        */
    }
    
    function oauth_token_filename($token)
    {
        return TMP_DIR.'/oauth.'.$token;
    }
    
    function oauth_save_token_details($token, $details)
    {
        if($fh = @fopen(oauth_token_filename($token), 'w'))
        {
            fwrite($fh, serialize($details));
            fclose($fh);
            return true;
        }
        
        return false;
    }
    
    function oauth_read_token_details($token)
    {
        if($fh = @fopen(oauth_token_filename($token), 'r'))
        {
            $content = fread($fh, 100000);
            fclose($fh);

            $details = unserialize($content);
            
            if($details === false)
                return false;
                
            return $details;
        }
        
        return false;
    }
    
?>
