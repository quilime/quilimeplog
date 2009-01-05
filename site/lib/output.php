<?php

    require_once 'Smarty/Smarty.class.php';

   /**
    * @return   Smarty  Locally-usable Smarty instance.
    */
    function get_smarty_instance()
    {
        $s = new Smarty();

        $s->compile_dir = join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'templates', 'cache'));
        $s->cache_dir = join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'templates', 'cache'));

        $s->template_dir = join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'templates'));
        $s->config_dir = join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'templates'));
        
        $s->register_modifier('url_domain', 'get_url_domain');
        $s->register_modifier('nice_relative_time', 'get_relative_time');
        
        $s->assign('domain', get_domain_name());
        $s->assign('base_dir', get_base_dir());
        $s->assign('base_href', get_base_href());
        $s->assign('constants', get_defined_constants());
        $s->assign('request', array('user' => get_request_user()));        
        
        return $s;
    }
    
   /**
    * @param    string  $format     "text", "xml", etc.
    * @param    string  $default    Default format
    * @return   array   Format, mime-type
    */
    function parse_format($format, $default)
    {
        $types = array('html' => 'text/html',
                       'text' => 'text/plain',
                       'atom' => 'application/atom+xml',
                       'json' => 'text/json',
                       'js'   => 'application/x-javascript',
                       'xspf' => 'application/xspf+xml',
                       'xml'  => 'text/xml',
                       'jpg'  => 'image/jpeg',
                       'png'  => 'image/png',
                       'm3u'  => 'audio/x-mpegurl');

        $format = empty($format) ? $default : $format;
        
        return array($format, $types[$format]);
    }
    
    
    function get_url_levels()
    {
        $params = substr($_SERVER['REQUEST_URI'], strlen(get_base_dir().'/'));;
        $parts  = explode('/', $params);
        for($i = 0; $i < count($parts); $i++) {
            if($parts[$i][0] == '?')
                $parts[$i] = null;
        }
        return array_filter($parts);
    }  
    
    
    function get_domain_name()
    {
        if(php_sapi_name() == 'cli') return CLI_DOMAIN_NAME;
        return $_SERVER['SERVER_NAME'];
    }
    
    
    function get_base_dir()
    {
        if(php_sapi_name() == 'cli') return CLI_BASE_DIRECTORY;
        return rtrim(dirname($_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR);
    }
    
    
    function get_include_dir()
    {
        return get_base_dir() .'/include';
    }
    
    
    function get_base_href()
    {
        if(php_sapi_name() == 'cli')    return '';
        $query_pos = strpos($_SERVER['REQUEST_URI'], '?');
        return ($query_pos === false) ? $_SERVER['REQUEST_URI']
                                      : substr($_SERVER['REQUEST_URI'], 0, $query_pos);
    }
    
    
    function get_url_domain($url)
    {
        $parsed = parse_url($url);
        return $parsed['host'];
    }
    
    
    /**
     *  Looks for {$include=<string>} inside the content of a project or a post 
     *  where <string> is a file that exists in the www/include directory
     *
     *  @param      string  $content    
     *  @return     string  content with included file inserted
     */
    function parse_include($content)
    {
        $pattern = '/\{\$include=.*?\}/';
        preg_match($pattern, $content, $includeFiles);
        
        if(sizeof($includeFiles) == 0)
            return $content;
            
        $file = $includeFiles[0];
        $parsed = str_replace('}', "", str_replace('{$include=', "", $file));
        $url = 'http://' . get_domain_name() . get_include_dir() .'/'. $parsed;        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $page = curl_exec($ch);
        curl_close($ch);
        return str_replace($file, $page, $content);
    }
    
    
   /**
    * @param    int     $seconds    Number of seconds to convert into a human-readable timestamp
    * @return   tring   Human-readable approximate timestamp like "2 hours"
    */
    function approximate_time($seconds)
    {
        switch(true)
        {
            case abs($seconds) <= 90:
                return 'moments';

            case abs($seconds) <= 90 * 60:
                return round(abs($seconds) / 60).' minutes';

            case abs($seconds) <= 36 * 60 * 60:
                return round(abs($seconds) / (60 * 60)).' hours';

            default:
                return round(abs($seconds) / (24 * 60 * 60)).' days';
        }
    }    
    
    
   /**
    * @param    int     $time   Unix timestamp
    * @return   string  Relative time string like "2 hours earlier"
    */
    function get_relative_time($time)
    {
        $diff = $time - time();
        return approximate_time($diff) . ($diff < 0 ? ' ago' : ' from now');
    }
    
    
    /**
     * Replace line breaks with <br />.  I don't usr nl2br because it doesn't remove the line breaks, it just adds the <br />
     * @param string $str
     * @return string
     */
    function clean($str) 
    {
      $str = str_replace("\r", "", $str);  // Remove \r
      $str = str_replace("\n", "<br />", $str);  // Replace \n with <br />
      return $str;
    }
    
    
  /**
   * This function cleans up a string and make it ready to be displayed in a textarea field.
   * Replaces <br /> with line breaks which is easier to read for the user.
   *
   * @param string $str
   * @return string
   */
    function clean_for_textarea($str) {
        $str = clean($str);
        $str = str_replace("<br />", "\n", $str);
        return $str;
    }


    function die_with_code($code, $message)
    {
        header("HTTP/1.1 {$code}");
        die($message);
    }

    
    function print_pre($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }    
?>
