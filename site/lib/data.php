<?php

    require_once 'PEAR.php';
    require_once 'DB/DB.php';
    require_once 'JSON.php';
    require_once 'auth.php';
    require_once 'oauth.php';
    require_once 'Crypt/HMAC.php';


    function &get_db_connection()
    {
        return DB::connect(DB_DSN);
    }


    function get_posts(&$dbh, $args=array())
    {
        $match = empty($args['match']) ? null : $args['match'];
        $order_by = empty($args['order_by']) ? null : $args['order_by'];
        $order = empty($args['order']) ? null : $args['order'];
        $count = is_numeric($args['count']) ? intval($args['count']) : 10;
        $offset = is_numeric($args['offset']) ? intval($args['offset']) : 0;
        $title = empty($args['title']) ? null : $args['title'];
        $slug = empty($args['slug']) ? null : $args['slug'];        
        $project_slug = empty($args['project_slug']) ? null : $args['project_slug'];
        $project_excludes = empty($args['project_excludes']) ? null : $args['project_excludes'];
        $tags = empty($args['tags']) ? null :  $args['tags'];                
        
        $where_clauses = array('1');
        
        if($match)
            $where_clauses[] = sprintf('(MATCH(p.content) AGAINST (%s))', $dbh->quoteSmart($match));

        if($tags)
            $where_clauses[] = sprintf('(MATCH(p.tags) AGAINST (%s))', $dbh->quoteSmart($tags));            

        if($title)
            $where_clauses[] = sprintf('p.title = %s', $dbh->quoteSmart($title));

        if($slug)
            $where_clauses[] = sprintf('p.slug = %s', $dbh->quoteSmart($slug));

        if($project_excludes) 
            foreach($project_excludes as $ex) 
                $where_clauses[] = sprintf('(pr.title != %s)', $dbh->quoteSmart($ex));

        if($project_slug)
            $where_clauses[] = sprintf('p.project_slug = %s', $dbh->quoteSmart($project_slug));

        $where_clause = join(' AND ', $where_clauses);
        
        switch($order_by)
        {
            case 'title':
                $order_clause = 'p.title ASC';
                break;        
                
            case 'project_slug':
                $order_clause = 'p.project_slug ASC, p.title ASC';
                break;                        
        
            case 'oldest':
                $order_clause = 'p.date ASC';
                break;
    
            case 'newest':
                $order_clause = 'p.date DESC';
                break;
            
            default :
                $order_clause = 'p.date DESC';
                break;
        }
        
        $limit_clause = "{$count} OFFSET {$offset}";
        
        $tables_clause = 'posts AS p';
        $projects_table = 'projects AS pr';
        
        $q = sprintf("SELECT SQL_CALC_FOUND_ROWS 
                            p.id, p.project_slug, p.title, p.tags,  
                            p.content, p.slug, p.hidden, p.comments_enabled, 
                            UNIX_TIMESTAMP(p.date) AS date,
                            pr.slug as project_slug, pr.title as project_title  
                      FROM  {$tables_clause}
                      LEFT JOIN {$projects_table} 
                            ON pr.slug = p.project_slug
                      WHERE {$where_clause}
                      ORDER BY {$order_clause}
                      LIMIT {$limit_clause}");

        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");
        
        $posts = array();
    
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {

            // special case for 'log' project
            // hacky
            if($row['project_slug'] == 'log') {
                $row['project_url'] = get_base_dir().'/'.$row['project_slug'].'/';
                $row['url'] =  $row['project_url'] . $row['slug'] . '/';
            } else {
                $row['project_url'] = get_base_dir().'/projects/'.$row['project_slug'].'/';
                $row['url'] = get_base_dir().'/projects/'.$row['project_slug'].'/'.$row['slug'].'/';
            }
            
            $tags_ex = explode(", ", $row['tags']);
            $tags = array();
            foreach($tags_ex as $tag) {
                $tags[] = '<a href="' . get_base_dir() . '/posts/tags/' . $tag . '/">' . $tag . '</a>';
            }
            $row['tags_imploded'] = implode(', ', $tags);
            $row['date_RFC'] = date('r', $row['date']);
            $row['date_formatted'] = date("m/j/Y", $row['date']) . " @ ". date("g:ia", $row['date']);
            $row['content_cleaned'] = str_replace('{$base_dir}', get_base_dir(), $row['content']);
            $posts[] = $row;
        }
    
        $res =& $dbh->query("SELECT FOUND_ROWS()");
        
        if(PEAR::isError($res))
            die('DB Error: (' . __FILE__ . ', ' . __LINE__ . ') '.$res->message);
          
        $total = end($res->fetchRow());

        return array($posts, $total);
    }    

    
    function get_projects(&$dbh, $args=array())
    {
        $match = empty($args['match']) ? null :  $args['match'];
        $order_by = empty($args['order_by']) ? null :  $args['order_by'];
        $excludes = empty($args['excludes']) ? null :  $args['excludes'];
        $show_hidden = empty($args['show_hidden']) ? false : $args['show_hidden'];
        $count = is_numeric($args['count']) ? intval( $args['count'] ) : 100;
        $offset = is_numeric($args['offset']) ? intval( $args['offset'] ) : 0;
        $title = empty($args['title']) ? null :  $args['title'];
        $type = empty($args['type']) ? null :  $args['type'];        
        $slug = empty($args['slug']) ? null :  $args['slug'];
        $tags = empty($args['tags']) ? null :  $args['tags'];        
                
        $where_clauses = array('1');
        
        if($match)
            $where_clauses[] = sprintf('(MATCH(p.content) AGAINST (%s))', $dbh->quoteSmart($match));

        if($tags)
            $where_clauses[] = sprintf('(MATCH(p.tags) AGAINST (%s))', $dbh->quoteSmart($tags));
    
        if($title)
            $where_clauses[] = sprintf('p.title = %s', $dbh->quoteSmart($title));
            
        if($type)
            $where_clauses[] = sprintf('p.type = %s', $dbh->quoteSmart($type));

        if(!$show_hidden && !$slug)
            $where_clauses[] = sprintf('p.hidden = 0', $dbh->quoteSmart($title));

        if($slug)
            $where_clauses[] = sprintf('p.slug = %s', $dbh->quoteSmart($slug));
    
        if($excludes) 
            foreach($excludes as $ex) 
                $where_clauses[] = sprintf('(p.slug != %s)', $dbh->quoteSmart($ex));
            
        $where_clause = join(' AND ', $where_clauses);

        switch($order_by)
        {
            case 'title':
                $order_clause = 'p.title ASC';
                break;

            case 'type':
                $order_clause = 'p.type ASC';
                break;                

            case 'oldest':
                $order_clause = 'p.date ASC';
                break;
    
            case 'newest':
            default :
                $order_clause = 'p.date DESC';
                break;
        }
        
        $limit_clause = "{$count} OFFSET {$offset}";
        
        $tables_clause = 'projects AS p';
        
        $q = sprintf("SELECT SQL_CALC_FOUND_ROWS 
                            p.id, p.title, p.type, p.content, 
                            p.tags, p.css, p.script,  
                            p.slug,  p.thumb_url,  p.hidden, 
                            UNIX_TIMESTAMP(p.date) AS date                                            
                      FROM  {$tables_clause}
                      WHERE {$where_clause}
                      ORDER BY {$order_clause}
                      LIMIT {$limit_clause}");

        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");
        
        $projects = array();
    
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
        {
            $tags_ex = explode(", ", $row['tags']);
            $tags = array();
            foreach($tags_ex as $tag) {
                $tags[] = '<a href="' . get_base_dir() . '/projects/tags/' . $tag . '/">' . $tag . '</a>';
            }
            $row['tags_imploded'] = implode(', ', $tags);
        
            $row['url'] = get_base_dir() . '/projects/' . $row['slug'] . '/';
            $row['thumb'] = get_base_dir(). '/thumbs/' . $row['slug'] . '-thumb.png';
            $row['content_cleaned'] = str_replace('{$base_dir}', get_base_dir(), $row['content']);
            $row['content_cleaned'] = parse_include($row['content_cleaned']);
            $projects[] = $row;
        }
    
        $res =& $dbh->query("SELECT FOUND_ROWS()");
        
        if(PEAR::isError($res))
            die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);
          
        $total = end($res->fetchRow());

        return array($projects, $total);
    }    
    
    
    function delete_project(&$dbh, $args = array())
    {
        $project_id = is_numeric($args['id']) ? intval($args['id']) : null;
        
        $where_clauses = array('1');
        
        if($project_id)
            $where_clauses[] = sprintf('id = %s', $dbh->quoteSmart($project_id));
            
        $where_clause = join(' AND ', $where_clauses);
        
        $tables_clause = 'projects';
        
        $limit_clause = "1";

        $q = sprintf("DELETE FROM {$tables_clause} 
                      WHERE  {$where_clause} 
                      LIMIT  {$limit_clause} ");
                      
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");

        return true;    
    }
    
    
    function delete_post(&$dbh, $args = array())
    {
        $post_id = is_numeric($args['id']) ? intval($args['id']) : null;
        
        $where_clauses = array('1');
        
        if($post_id)
            $where_clauses[] = sprintf('id = %s', $dbh->quoteSmart($post_id));
            
        $where_clause = join(' AND ', $where_clauses);
        
        $tables_clause = 'posts';
        
        $limit_clause = "1";

        $q = sprintf("DELETE FROM {$tables_clause} 
                      WHERE  {$where_clause} 
                      LIMIT  {$limit_clause} ");
                      
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");

        return true;    
    }    
    
    
    
    function update_setting(&$dbh, $args = array())
    {
        $title = empty($args['title']) ? null :  $args['title'];
        $value = empty($args['value']) ? null :  $args['value'];
        
        $where_clauses = array('1');
        
        if($title)
            $where_clauses[] = sprintf('s.title = %s', $dbh->quoteSmart($title));
            
        $where_clause = join(' AND ', $where_clauses);        
        
        $set_clauses = array();
        
        if($value)
            $set_clauses[] = sprintf('s.value = %s', $dbh->quoteSmart($value));
            
        $set_clause = join(', ', $set_clauses);    
        
        $tables_clause = 'settings AS s';   
        
        $limit_clause = "1";
        
        $q = sprintf("UPDATE {$tables_clause}
                      SET    {$set_clause}
                      WHERE  {$where_clause}
                      LIMIT  {$limit_clause}");           
       
        $res = $dbh->query($q);       
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");

        return true;
    }
    
    
    
    function get_sorted_project_list(&$dbh, $args=array())
    {    
        $show_hidden = empty($args['show_hidden']) ? false : $args['show_hidden'];
    
        $project_list_order = get_settings($dbh, array('title' => 'project_list_order'));
        $values = explode("&", $project_list_order[0]['value']);
        
        // get ids from seralized list in DB
        $list        = array();
        $project_ids = array();
        $section_ids = array();
        
        foreach($values as $val)
        {
            $list[] = $setting = explode("[]=", $val);
            switch($setting[0]) {
                case 'project' :    $project_ids[] = $setting[1]; break;
                case 'section' :    $section_ids[] = $setting[1]; break;
            }
        }
        
        // get projects
        $where_clauses = array('1');
        
        if(!$show_hidden)
            $where_clauses[] = 'p.hidden = 0';
        
        $project_in_clause = join(', ', $project_ids);
        $where_clauses[] = sprintf('id IN (%s)', $project_in_clause);
        $where_clause = join(' AND ', $where_clauses);        
        
        $q = sprintf("SELECT p.id, p.title, p.slug, p.hidden, UNIX_TIMESTAMP(p.date) AS date                                           
                      FROM projects AS p
                      WHERE {$where_clause}");
        $res = $dbh->query($q);
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");
        $projects = array();
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
        {
            $row['url'] = get_base_dir().'/'.PROJECTS.'/'.$row['slug'].'/';
            $projects[$row['id']] = $row;
        }        

        // get sections
        $section_in_clause = join(', ', $section_ids);
        $q = sprintf("SELECT s.id, s.title, s.slug                                      
                      FROM sections AS s
                      WHERE id 
                      IN ({$section_in_clause})");
        $res = $dbh->query($q);
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");
        $sections = array();
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
        {
            $sections[$row['id']] = $row;
        }
        
        $content = array('project' => $projects, 'section' => $sections);
        
        $ordered_list = array();        
        
        foreach($list as $item)
        {
            if($item[0] == 'spacer') {
                $ordered_list[] = array('title' => '&nbsp;', 'type' => 'spacer', 'id' => $item[1]);
                continue;
            }
            
            // if an id has been deleted...
            if(!$content[$item[0]][$item[1]])
                continue;
            
            $content[$item[0]][$item[1]]['type'] = $item[0];
            $ordered_list[] = $content[$item[0]][$item[1]];
        }
        
        return $ordered_list;
    }
    
        
    
    function get_settings(&$dbh, $args=array())
    {        
        $title = empty($args['title']) ? null :  $args['title'];
    
        $where_clauses = array('1');
        
        if($title)
            $where_clauses[] = sprintf('s.title = %s', $dbh->quoteSmart($title));        
            
        $where_clause = join(' AND ', $where_clauses);
                
        $tables_clause = 'settings AS s';
        
        $q = sprintf("SELECT SQL_CALC_FOUND_ROWS 
                            s.title, s.value
                      FROM  {$tables_clause}
                      WHERE {$where_clause}");

        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");

        $settings = array();
    
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
        {
            $settings[] = $row;
        }
    
        $res =& $dbh->query("SELECT FOUND_ROWS()");
        
        if(PEAR::isError($res))
            die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);
          
        $total = end($res->fetchRow());

        return $settings;
    }  
    
    
    function update_post(&$dbh, $args = array())
    {
        $post_id = is_numeric($args['id']) ? intval($args['id']) : null;
        $title = empty($args['title']) ? null :  $args['title'];
        $slug = empty($args['slug']) ? null :  $args['slug'];    
        $hidden = empty($args['hidden']) ? '0' : $args['hidden'] == 'true' ? '1' : '0';        
        $content = empty($args['content']) ? null :  $args['content'];
        $tags = empty($args['tags']) ? null :  $args['tags'];
        
        $where_clauses = array('1');
        
        if($post_id)
            $where_clauses[] = sprintf('p.id = %s', $dbh->quoteSmart($post_id));
            
        $where_clause = join(' AND ', $where_clauses);
        
        $set_clauses = array();
        
        if($content)
            $set_clauses[] = sprintf('p.content = %s', $dbh->quoteSmart($content));
            
        if($title)
            $set_clauses[] = sprintf('p.title = %s', $dbh->quoteSmart($title));
            
        if($slug)
            $set_clauses[] = sprintf('p.slug = %s', $dbh->quoteSmart($slug));

        if($tags)
            $set_clauses[] = sprintf('p.tags = %s', $dbh->quoteSmart($tags));
            
        if($hidden == '0' || $hidden == '1') // hacky
            $set_clauses[] = sprintf('p.hidden = %s', $dbh->quoteSmart($hidden));
        
        $set_clause = join(', ', $set_clauses);

        $tables_clause = 'posts AS p';
        
        $limit_clause = "1";
        
        $q = sprintf("UPDATE {$tables_clause}
                      SET    {$set_clause}
                      WHERE  {$where_clause}
                      LIMIT  {$limit_clause}");           
                      
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");

        return true;
    }
    

    function update_project(&$dbh, $args = array())
    {
        $project_id = is_numeric($args['id']) ? intval($args['id']) : null;
        $title   = empty($args['title']) ? null :  $args['title'];
        $slug    = empty($args['slug']) ? null :  $args['slug'];    
        $hidden  = empty($args['hidden']) ? '0' : $args['hidden'] == 'true' ? '1' : '0';        
        $content = empty($args['content']) ? null :  $args['content'];
        $tags    = empty($args['tags']) ? null :  $args['tags'];        
        $css     = empty($args['css']) ? null :  $args['css'];
        $script  = empty($args['script']) ? null :  $args['script'];
        
        $where_clauses = array('1');
        
        if($project_id)
            $where_clauses[] = sprintf('p.id = %s', $dbh->quoteSmart($project_id));
            
        $where_clause = join(' AND ', $where_clauses);
        
        $set_clauses = array();
        
        if($content)
            $set_clauses[] = sprintf('p.content = %s', $dbh->quoteSmart($content));
            
        if($css)
            $set_clauses[] = sprintf('p.css = %s', $dbh->quoteSmart($css));

        if($script)
            $set_clauses[] = sprintf('p.script = %s', $dbh->quoteSmart($script));

        if($tags)
            $set_clauses[] = sprintf('p.tags = %s', $dbh->quoteSmart($tags));            

        if($title)
            $set_clauses[] = sprintf('p.title = %s', $dbh->quoteSmart($title));
            
        if($slug)
            $set_clauses[] = sprintf('p.slug = %s', $dbh->quoteSmart($slug));
            
        if($hidden == '0' || $hidden == '1') // hacky
            $set_clauses[] = sprintf('p.hidden = %s', $dbh->quoteSmart($hidden));
        
        $set_clause = join(', ', $set_clauses);

        $tables_clause = 'projects AS p';
        
        $limit_clause = "1";
        
        $q = sprintf("UPDATE {$tables_clause}
                      SET    {$set_clause}
                      WHERE  {$where_clause}
                      LIMIT  {$limit_clause}");           
                      
        $res = $dbh->query($q);
        
        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");

        return true;
    }


    function insert_new_post(&$dbh, $args = array())
    {
		$project_slug = isset($args['project_slug']) ? $args['project_slug'] : false;
	
        $q = sprintf("INSERT INTO posts 
                             SET  title        = %s,
							 	  project_slug = %s,
                                  slug         = %s,
                                  tags         = 'note',
                                  date         = NOW()",
                    $dbh->quoteSmart("New Post - " . time()),
                    $dbh->quoteSmart($project_slug),
                    $dbh->quoteSmart("new-post-" . time()));
    
        $res = $dbh->query($q);

        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");  
            
        $res =& $dbh->query("SELECT * FROM posts where id = LAST_INSERT_ID()");  
  
        $new_post = array();
  
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $new_post[] = $row;
        }
        
        if(PEAR::isError($res))
            die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);

        return $new_post;
    }
	
	
    function insert_new_project(&$dbh)
    {
        $q = sprintf("INSERT INTO projects 
                             SET  title   = %s,
                                  slug    = %s,
                                  content = %s,
                                  tags    = 'project',
                                  css     = %s,  
                                  script  = %s,
                                  date    = NOW()",
                    $dbh->quoteSmart("New Project - " . time()),
                    $dbh->quoteSmart("new-project-" . time()),
                    $dbh->quoteSmart("new project content"),
                    $dbh->quoteSmart("/* css */"),
                    $dbh->quoteSmart("/* javascript */"));
    
        $res = $dbh->query($q);

        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");  
                
        $res =& $dbh->query("SELECT * FROM projects where id = LAST_INSERT_ID()");  
  
        $new_project = array();
  
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $new_project[] = $row;
        }
        
        if(PEAR::isError($res))
            die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);

        return $new_project;
    }	
    
    
    function insert_new_section(&$dbh)
    {
        $res = $dbh->query('INSERT INTO sections 
                            (   title,
                                slug,
                                date
                            )
                            VALUES(
                                '.$dbh->quoteSmart("New Section").', 
                                '.$dbh->quoteSmart("new-section").',
                                NOW()
                            )');

        if(PEAR::isError($res))
            die("DB Error: ".$res->message."\n{$q}");  
            
        $res =& $dbh->query("SELECT * FROM sections where id = LAST_INSERT_ID()");  
  
        $new_section = array();
  
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $new_section[] = $row;
        }
        
        if(PEAR::isError($res))
            die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);

        return $new_section;
    }
    
    
    
   /**
    * @param    $dbh                MDB2    Open database handle
    */
    function set_request_user_from_cookie(&$dbh)
    {
        global $request_authenticated_user_name;
        
        
        if($_COOKIE[SESSION_COOKIE_NAME])
        {
            $q = sprintf("SELECT user_name
                          FROM tokens AS t
                          WHERE id = %s
                            AND deleted = 0",
                         $dbh->quoteSmart($_COOKIE[SESSION_COOKIE_NAME]));

            $res = $dbh->query($q);
            
            if(PEAR::isError($res))
                die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);
            
            if($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                $dbh->query('UPDATE proj_tokens SET time = NOW() WHERE id = '.$dbh->quoteSmart($_COOKIE[SESSION_COOKIE_NAME]));
                $request_authenticated_user_name = $row['user_name'];
            }

            return get_request_user();
        }
        
        return null;
    }
    
   /**
    * @param    $dbh                MDB2    Open database handle
    * @param    $username           string  Optional user name.
    *                                       If non-null, causes cookies and OAuth to be ignored.
    */
    function set_request_user(&$dbh, $username=null)
    {
        global $request_authenticated_user_name;
        
        if($username || $username === false)
        {
            $request_authenticated_user_name = $username;
            return get_request_user();
        }
        
        if($oauth = oauth_parameters())
        {
            $q = sprintf("SELECT secret, user_name
                          FROM proj_tokens
                          WHERE id = %s
                            AND deleted = 0",
                         $dbh->quoteSmart($oauth['token']));
            
            $res = $dbh->query($q);
            
            if(PEAR::isError($res))
                die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);
            
            if($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                $oauth['token_secret'] = $row['secret'];
                $user_name = $row['user_name'];
            }

            $q = sprintf("SELECT secret
                          FROM proj_consumers
                          WHERE id = %s",
                         $dbh->quoteSmart($oauth['consumer_key']));
            
            $res = $dbh->query($q);
            
            if(PEAR::isError($res))
                die('DB Error: ('.__FILE__.', '.__LINE__.') '.$res->message);
            
            if($row = $res->fetchRow(DB_FETCHMODE_ASSOC))
                $oauth['consumer_secret'] = $row['secret'];

            if(oauth_valid_signature($oauth['consumer_secret'], $oauth['token_secret'])) {
                $dbh->query('UPDATE proj_tokens SET time = NOW() WHERE id = '.$dbh->quoteSmart($oauth['token']));
                $request_authenticated_user_name = $user_name;
                return get_request_user();
            }
            
            return null;
        }

        if($_COOKIE[SESSION_COOKIE_NAME])
            return set_request_user_from_cookie($dbh);
        
        return null;
    }
    
    function get_request_user()
    {
        global $request_authenticated_user_name;
        return $request_authenticated_user_name;
    }
    
    
?>
