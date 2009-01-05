<?php
    
    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.'../lib');
    require_once 'init.php';
    require_once 'data.php';
    require_once 'output.php';
    
    list($response_format, $response_mime_type) = parse_format($_GET['format'], 'html');
    
    $params = array();
    $params['added_after']  = isset($_GET['added-after'])  ? $_GET['added-after'] : null;
    $params['added_before'] = isset($_GET['added-before']) ? $_GET['added-before'] : null;
    $params['order_by']     = isset($_GET['order-by'])     ? $_GET['order-by'] : null;
    $params['order']        = isset($_GET['order'])        ? $_GET['order'] : null;
    $params['count']        = is_numeric($_GET['count'])   ? intval($_GET['count']) : 100;
    $params['offset']       = is_numeric($_GET['offset'])  ? intval($_GET['offset']) : 0;    
    
    $url_levels = get_url_levels();
    $slug1 = $url_levels[1];
    $slug2 = $url_levels[2];

    $template = 'index';

    $dbh =& get_db_connection();
    set_request_user($dbh);
    
    $sm  = get_smarty_instance();    


    // base
    if(count($url_levels) == 0)
    {
        list($projects, $projects_total) = get_projects($dbh, array('type' => 'project'));
        list($articles, $articles_total) = get_projects($dbh, array('show_hidden' => true, 'type' => 'article'));
        $sm->assign('projects_total',  $projects_total);
        $sm->assign('articles_total',  $articles_total);
    }
    // first url level    
    else if(count($url_levels) == 1) 
    {
    
        switch($url_levels[0])
        {
            case 'projects' :
                $template = 'projects';
                $title = 'projects';
                $params['show_hidden'] = get_request_user() ? true : false;
                $params['order_by'] = $params['order_by'] ? $params['order_by'] : 'newest';
                $params['excludes'] = array('bookmarks', 'links', 'info', 'sketchbook', 'log', 'aggregate', 'reading');
                // $sorted_project_list = get_sorted_project_list($dbh, $params);
                list($projects, $total) = get_projects($dbh, $params);
                break;
                
            case 'project-list' :
                $template = 'project-list';
                $title = 'project list';
                $params['show_hidden'] = get_request_user() ? true : false;
                $params['order_by'] = $params['order_by'] ? $params['order_by'] : 'newest';
                $params['excludes'] = array('links', 'info', 'sketchbook', 'blog');
                $sorted_project_list = get_sorted_project_list($dbh, $params);
                break;
            
            case 'sections' :
                $template = 'sections';
                $title = 'sections';
                $params['order_by'] = $params['order_by'] ? $params['order_by'] : 'newest';
                list($sections, $total) = get_sections($dbh, $params);
                break;

            case 'log' :
            case 'posts'  :
                $template = 'posts';
                $title = 'notes, process';
                $hide_parent_link = true;
                $params['order_by'] = $params['order_by'] ? $params['order_by'] : 'date';
                list($posts, $total) = get_posts($dbh, $params);
                break;

            case 'sketchbook' :    
            case 'links' : 
            case 'info'  :
            case 'bookmarks' :
            case 'reading'  : 
            case 'aggregate' :
                $template = 'project';        
                $hide_back_link = true;
                $hide_archive = true;
                $params['slug'] = $url_levels[0];
                list($projects, $total) = get_projects($dbh, $params);
                $post_params['order_by'] = $params['order_by'] ? $params['order_by'] : 'date';
                $post_params['project_slug'] = $params['slug'];
                list($posts, $total) = get_posts($dbh, $post_params);
                $project = $projects[0];
                break;                
        }
    }    
    // second url level
    else if (count($url_levels) == 2)
    {    
        if($url_levels[0] == 'projects') {
            $template = 'project';
            $parent_page = "projects/";
            $params['slug'] = $url_levels[1];
            list($projects, $total) = get_projects($dbh, $params);
            if($total >= 1) {
                $project = $projects[0];              
                $post_params = array();
                $post_params['project_slug'] = $project['title'];            
                list($posts, $total) = get_posts($dbh, $post_params);
            }
            else {
                $project = null;
            }
        }
        if($url_levels[0] == 'posts' || $url_levels[0] == 'log') {
            $template = 'post';
            $parent_page = $url_levels[0].'/';
            $params['slug'] = $url_levels[1];
            list($posts, $total) = get_posts($dbh, $params);
            if($total == 1)  
                $post = $posts[0];
            else 
                $post = null;                
        }        
    }
    // third url level
    else if(count($url_levels) > 2)
    {
        if($url_levels[1] == 'tags') {
            switch($url_levels[0]) {
                case 'projects' : 
                    $template = 'projects';
                    $title = 'projects tagged as ' . $url_levels[2];
                    $tags = $url_levels[2];
                    $params['tags'] = $tags;
                    $params['show_hidden'] = get_request_user() ? true : false;
                    $params['order_by'] = $params['order_by'] ? $params['order_by'] : 'newest';
                    $params['excludes'] = array('bookmarks', 'links', 'info', 'sketchbook', 'log', 'aggregate', 'reading');
                    list($projects, $total) = get_projects($dbh, $params);
                    break;                
                case 'posts' : 
                    $template = 'posts';
                    $title = 'notes, process';
                    $hide_parent_link = true;
                    $params['tags'] = $url_levels[2];
                    $params['order_by'] = $params['order_by'] ? $params['order_by'] : 'date';
                    list($posts, $total) = get_posts($dbh, $params);
                    break;
            }
        }
        else {
            $template = 'post';
            $params['slug'] = $url_levels[2];
            list($posts, $total) = get_posts($dbh, $params);
            if($total == 1)  
                $post = $posts[0];
            else 
                $post = null;
        }
    }
    $dbh->disconnect();
    
    $sm  = get_smarty_instance();    
    
    $sm->assign('count',  $params['count']);
    $sm->assign('offset', $params['offset']);
    $sm->assign('total',  $total);
    
    $sm->assign('parent_page', $parent_page);
    
    $sm->assign('post', $post);
    $sm->assign('posts', $posts);
    $sm->assign('tags', $tags);
    
    $sm->assign('edit', $edit);

    $sm->assign('project', $project);
    $sm->assign('cur_project', $cur_project[0]);
    
    $sm->assign('projects', $projects);
    $sm->assign('articles', $articles);
    
    $sm->assign('sorted_project_list', $sorted_project_list);

    $sm->assign('hide_parent_link', $hide_parent_link);
    $sm->assign('hide_back_link',  $hide_back_link);
    $sm->assign('hide_archive',  $hide_archive);

    $sm->assign('title', $title);

    // output
    header("Content-Type: {$response_mime_type}; charset=UTF-8");
    print $sm->fetch($template . ".{$response_format}.tpl");
        
?>
