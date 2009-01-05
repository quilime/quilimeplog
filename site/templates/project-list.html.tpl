<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / {$title|@strtolower}</title>

    {include file="header.html.tpl"}
    
    {if $request.user}
        <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />
        <script src="{$base_dir}/jquery-1.2.6.min.js" type="text/javascript" language="javascript1.2"></script>             
        <script src="{$base_dir}/jquery-ui-1.6rc2.min.js" type="text/javascript" language="javascript1.2"></script>         
        <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
        <script>
        var base_dir = '{$base_dir}';
        var page = 'projects';
        </script>
    {/if}
    
</head>
<body>
    
    {include file="nav.html.tpl"}
    
    {if $request.user}
        {include file="admin.html.tpl" page="projects"}
    {/if}
    
    <div id="main">
    
        <h1>{$title}</h1>    
        
        <div id="content">
        
        {if $request.user}
        <div class="admin_object">
            <input value="new project" id="insert_project" type="button" /><br/>
            <input value="new section" id="insert_section" type="button" /><br/>
            <input value="new spacer"  id="insert_spacer" type="button" />
            <br/><br/>
        </div>
        {/if}


        <ul id="projects">
            {foreach item="item" from=$sorted_project_list}
            <li id="{$item.type}_{$item.id}" class="{$item.type}{if $item.hidden == 1} admin_object hidden{/if}">
                {if $request.user}
                    <div class="admin_object handle"></div>
                    {if $item.type == 'spacer'}
                    <input type="button" value="x" class="admin_object spacer_del"/>
                    {/if}
                {/if}
                {if $item.type == 'project' || $item.type == 'post'}
                <a href="{$item.url}/">
                    {$item.title}
                </a>
                {elseif $item.type == 'section'}
                {$item.title}
                {if $request.user}
                <input type="button" onClick="edit_section_title(this, '{$item.title|escape}', {$item.id});" value="edit" />
                {/if}
                {else}
                    {$item.title}
                {/if}    
                
            </li>            
            {/foreach}        
        </ul>
        
        </div>
        
    </div>

</body>
</html>
