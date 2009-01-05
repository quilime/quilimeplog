<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / upload thumb</title>

    {include file="header.html.tpl"}
    
    {if $request.user}
        <script src="{$base_dir}/jquery-ui-1.6rc2.min.js" type="text/javascript" language="javascript1.2"></script>        
        <script src="{$base_dir}/admin.js" type="text/javascript" language="javascript1.2"></script>
        <link rel="StyleSheet" href="{$base_dir}/admin.css" type="text/css" />        
    {/if}

    {literal}    
    <script type="text/javascript" language="javascript1.2">
    </script>
    
    <style style="text/css">
        body { background:#fff; padding:20px; }
        .thumb_holder { width:160px; height:160px; }
    </style>
    {/literal}    
    
</head>
<body>

    {if $request.user}
        <div class="thumb_holder"><img src="{$base_dir}/thumbs/{$slug}-thumb.png" border="0"/></div>
        <form id="upload_thumb" enctype="multipart/form-data" action="{$base_dir}/upload.php?type=thumb&slug={$slug}" method="POST">
            <input type="file" name="uploadedfile" />
            <input type="submit" value="upload" />
        </form>
    {/if}
    
</body>
</html>
