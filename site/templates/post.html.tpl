<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / {$post.project_title} / {$post.title}</title>

    {include file="header.html.tpl"}

    {if $request.user}
        <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />
        <script src="{$base_dir}/jquery-ui-1.6rc2.min.js" type="text/javascript" language="javascript1.2"></script>        
        <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
        <script>
        var base_dir = '{$base_dir}';
        var type = 'post';
        </script>        
    {/if}
    
</head>
<body>

    {if $request.user}
        {include file="edit_panel.html.tpl" edit="post"}
    {/if}

    {include file="nav.html.tpl"}
    
    <div id="main">

        <div class="post">
            <h1><a href="{$post.url}">{$post.title}</a></h1>
            <div class="date">
            {$post.date_formatted} under <span class="tags">{$post.tags_imploded}</span>
            </div>
            <div id="content" class="content" style="margin-top:10px;">
                    {$post.content_cleaned}
            </div>
        </div>

        <div class="comments">
            <h1>comments (0)</h1>
            
            <form name="commentform">
            
            <label>name</label><br/><input type="text" name="name" value="name" /><br/><br/>
            <label>www (optional)</label><br/><input type="text" name="www" value ="www" /><br/><br/>
            <label>comment</label><br/>
            <textarea>comment</textarea>

            <br/><br/>

            <input type="submit" value="comment"/>
            </form>
                
            
            <br>
            -- list comments
            <br/>
                
            <p>
            <a href="">rss</a>
            </p>
                
        </div>  
        
    </div>

</body>
</html>
