<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

	<title>{$smarty.const.SITE_TITLE} / browser</title>

    <script src="{$base_dir}/jquery-1.2.6.min.js"  type="text/javascript"></script>
    <script src="{$base_dir}/jquery.easing.1.3.js" type="text/javascript"></script>
    <script src="{$base_dir}/jqueryFileTree.js"    type="text/javascript"></script>

    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="jqueryFileTree.css" />

    <script language="javascript" type="text/javascript">

    var base = '{$base_dir}';

    {literal}
    
    $(document).ready( function() {
       $('#filelist').fileTree({ root: 'media/', expandSpeed: 50, collapseSpeed:50 }, function(file) {
            var f = file;
            var insert = "\n" + '<img src="{$base_dir}/' + f + '" />';
            opener.document.getElementById('edit_content').value += insert;
        });
    });
    
    {/literal}
    
    </script>
    
    
    
    
</head>
<body>

<div id="filelist">

</div>

</body>
</html>
