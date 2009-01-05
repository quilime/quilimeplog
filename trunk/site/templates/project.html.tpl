<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / {$project.title}</title>

    {include file="header.html.tpl"}
    
    {if $request.user}
        <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
        <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />      
        <script>
        var base_dir = '{$base_dir}';
        </script>           
    {/if}
    
    <script type="text/javascript" language="javascript1.2">
    {$project.script}
    </script>
    
    <style style="text/css">
    {$project.css}
    </style>
    
</head>
<body>

    {if $request.user}
        {include file="edit_panel.html.tpl" edit="project"}
    {/if}
    
    {include file="nav.html.tpl"}
        
    <div id="main">

        {if !$hide_back_link}
        <a id="parent" href="{$base_dir}/projects/">&larr;</a>
        {/if}
    
        <div class="content">
            
            <div id="content">
            	{$project.content_cleaned}
            </div>            
            
        </div>
        
        {*
        {if !$hide_archive}
            <div class="desc tags">
            archived as {$project.tags_imploded}
            </div>
        {/if}
        *}
        
        {if $posts|@count > 0}
        
        {*      
        <p>
        <a href="#" id="process_link" onClick="$('#posts').toggle(); return false;">process</a>
        </p>
        *}
        
        <div id="posts">
            {foreach item="post" from=$posts}
            <div class="post">
                <h1>
                    <span>
                    <a href="{$post.url}">{$post.title}</a>
                    </span>            
                </h1>
                <div class="date">{$post.date_formatted}</div>
                <div class="content">
                    {$post.content}
                </div>
            </div>
            {/foreach}        
        </div>        
        
        <br/><br/>
        
        {*
        
        <br/><br/><br/>
        
        <a href="#">view more . . .</a>
        
        <br/><br/>
        
        <a href="#">subscribe to rss</a>        
        
        <br/><br/><br/>
        
        *}
        
        {/if}
        
           

    </div>

</body>
</html>
