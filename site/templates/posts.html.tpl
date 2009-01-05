<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / {$title|@strtolower}</title>

    {include file="header.html.tpl"}
    
    <link rel="alternate" type="application/rss+xml" title="log rss feed" href="?format=xml" />
    
    {if $request.user}
        <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />
        <script src="{$base_dir}/jquery-ui-1.6rc2.min.js" type="text/javascript" language="javascript1.2"></script>         <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
        <script>
        var base_dir = '{$base_dir}';
        var type = 'post';
        </script>        
    {/if}
    
</head>
<body>

    {if $request.user}
        <input type="button" onClick="insert_post('log');" value="insert post" />
    {/if}

    {include file="nav.html.tpl"}

    <div id="main">

        <h1>{$title}</h1>

        {foreach item="post" from=$posts}
        <div class="post {if $post.hidden == 1}hidden{/if}" >
            <h1>
				<a href="{$post.url}">{$post.title}</a>
            </h1>      
            
            <span class="date">               
            {$post.date_formatted} under <span class="tags">{$post.tags_imploded}</span>
            {if !$hide_parent_link}
            <a id="parent_link" title="{$post.project_title}" href="{$post.project_url}">&larr;</a>
            {/if}
            </span>				            
            
            <div class="content">
                {$post.content_cleaned}
            </div>
    
            <div class="comments">
            {if $post.comments_enabled}
            <a href="#comment">+ [ 0 ]</a>
            {/if}
            </div>
            
        </div>
        {/foreach}
        


        <br/><br/><br/>

        <a href="#">next . . .</a>

        <br/><br/>

        <a href="?format=xml">rss</a>
        
    </div>

</body>
</html>
