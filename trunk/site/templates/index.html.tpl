<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE}</title>

    {include file="header.html.tpl"}
    
    {literal}
    
        <style>
        
            .col { width: 300px; float:left; }
        
        </style>
    
    {/literal}
    
</head>
<body>
    
    <div id="main">

        <h1>{$smarty.const.SITE_TITLE}</h1>

        <div class="col">
            <strong>projects</strong>
            <br/><br/>
            <ul>
                {foreach name=project from=$projects item=project}
                    <li><a href="{$project.url}">{$project.title}</a></li>
                {/foreach}
            </ul>
        </div>
        
        <div class="col">
            <strong>articles</strong>
            <br/><br/>
            <ul>
                {foreach name=article from=$articles item=article}
                    <li>{$article.title}</li>
                {/foreach}
            </ul>
        </div>        
        
    </div>

</body>
</html>
