<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / {$title|@strtolower}</title>

    {include file="header.html.tpl"}
    
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

    {include file="nav.html.tpl"}
    
    {if $request.user}
        {include file="admin.html.tpl" page="projects"}
    {/if}

    <div id="main">

        <h1>{$title}&nbsp;</h1>
        
        <ul>
        {foreach item="section" from=$sections}
        <li>
            {$section.title}
        </li>
        {/foreach}
        </ul>
        
    </div>

</body>
</html>
