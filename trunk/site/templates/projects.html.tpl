<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / {$title|@strtolower}</title>

    {include file="header.html.tpl"}
    
    {if $request.user}        
        <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
        <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />
        <script>
        var base_dir = '{$base_dir}';
        </script>
    {/if}
    
</head>
<body>

    {if $request.user}
        {include file="edit_projects.html.tpl" edit="projects"}
    {/if}
    
    <div id="main">    
            
        <div id="content">
            <ul id="projects">
                {foreach item="project" from=$projects}
                <li {if $project.hidden==1}class="hidden"{/if}>
                    <a href="{$project.url}">
                    <div class="thumb">
                    <img src="{$project.thumb}" border="0" />
                    </div>
                    <span>{$project.title}</span>
                    </a>
                </li>            
                {/foreach}    
                <li>
                <a href="{$base_dir}/info/">
                <div class="thumb">
                <img src="{$base_dir}/thumbs/info-thumb.png" border="0" />
                </div>
                <span>info</span>
                </a>
                </li>
            </ul>            
        </div>
        
    </div>

</body>
</html>
