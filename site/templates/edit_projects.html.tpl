{if $request.user}

    <a id="toggle" href="javascript:toggle_edit_panel();">&rarr;</a>

    <div id="edit_panel" class="admin_object">

        <div class="menu">
            <form action="{$base_dir}/login.php" method="post" id="logout">
                    <input type="submit" name="action" value="log out" class="submit">
            </form>       
            
            <div id="status" style="display:none;"></div>
        </div>

        <form id="edit_form" action="javascript:edit_form_success();" method="POST">
            
            <input type="button" onClick="insert_project();" value="insert project" />            
            
        </form>
    </div>

{/if}
