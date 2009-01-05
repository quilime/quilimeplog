<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / edit</title>

    {include file="header.html.tpl"}
    
    <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />
    <script src="{$base_dir}/jquery-1.2.6.min.js" type="text/javascript" language="javascript1.2"></script>             
    <script src="{$base_dir}/jquery-ui-1.6rc2.min.js" type="text/javascript" language="javascript1.2"></script>         
    <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
    <script>
    var base_dir = '{$base_dir}';
    var page = 'projects';
    </script>

    
    {literal}
    
    <style>
        
        label { display:block; }
        textarea { width:600px; height:300px; padding:20px 10px;  }
    
    </style>
    
    {/literal}
    
</head>
<body>

    {if $request.user}
    
    <div id="projects" style="float:left;">
        
        <h1>all projects</h1>
        
        <p>
        <a href="?edit=projectlist">
        &rarr; edit list order
        </a>
        </p>
        
        <ul id="project_list">
            {foreach item="project" from=$projects}
            <li>
                <a href="?edit=project&slug={$project.slug}">{$project.title}</a>
            </li>
            {/foreach}
        </ul>
    </div>

    <div id="edit_area" style="float:left;width:600px;">
    
        {if $edit == 'project'}    
            {include file="edit_project.html.tpl"}
        {elseif $edit == 'projectlist'}
            {include file="edit_projectlist.html.tpl"}
        {/if}
        
    </div>
    
    {/if}
    
</body>
</html>
