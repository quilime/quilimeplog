<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>{$constants.SITE_TITLE} log In</title>
    
    {include file="header.html.tpl"}
    <link rel="stylesheet" href="{$base_dir}/admin.css" type="text/css">    

    {if $authenticated.callback}
        <meta http-equiv="refresh" content="1; {$authenticated.callback|escape}">
    {/if}

</head>
<body>

    <div id="page">

        {if $authenticated.attempt}
            {if $authenticated.success}
                <h3>hi {$request.user|escape}</h3>
            {else}
                <h3>not ok</h3>
            {/if}
        {/if}
        
        {if $authenticated.callback}
            <p>Logging in...</p>
            <a href="{$authenticated.callback|escape}">{$authenticated.callback|escape}</a>...
        {/if}
    
        {if $request.user && !$authenticated.attempt}
            
            <form action="{$base_dir}/login.php" method="post" id="logout">
                <input type="submit" name="action" value="log out" class="submit">
            </form>
            
        {elseif !$authenticated.attempt}
            <form action="{$base_dir}/login.php" method="post" id="login" />
                <input onBlur="this.className='blur';" onFocus="this.className='focus';" class="blur" name="username" type="text" size="16" /><br/>
                <input onBlur="this.className='blur';" onFocus="this.className='focus';" class="blur" name="password" type="password" size="16" /><br/>
                <input type="submit" class="submit" name="action" value="log in" />
                <input type="hidden" name="callback" value="{$reffering_url}" />
            </form>
            <script language="javascript">
                document.forms[0].username.focus();
            </script>
        {/if}

    </div>
    
</body>
</html>
