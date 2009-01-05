{if $request.user}

    {if $edit == 'post'}
        {assign var="item" value=$post}
    {else $edit == 'project'}
        {assign var="item" value=$project}
    {/if}

    <a id="toggle" href="javascript:toggle_edit_panel();">&rarr;</a>

    <div id="edit_panel" class="admin_object">

        <div class="menu">
            <form action="{$base_dir}/login.php" method="post" id="logout">
                    <input type="button" value="media browser" id="media_browser" > 
                    <input type="submit" name="action" value="log out" class="submit">
            </form>       
            <div id="status" style="display:none;"></div>
        </div>

        <form id="edit_form" action="javascript:edit_form_success();" method="POST">

            <p>
            <label>Title</label><input type="text" name="edit_title" id="edit_title" value="{$item.title|@escape}" />&nbsp;&nbsp;&nbsp;
            <label>Slug</label><input type="text" name="edit_slug" id="edit_slug" value="{$item.slug|@escape}" />&nbsp;&nbsp;&nbsp;
            <label for="edit_hidden">Hidden</label><input type="checkbox" name="edit_hidden" id="edit_hidden" {if $item.hidden == 1}checked{/if} />
            </p>
            
            <p>
            <label>Tags</label><input type="text" name="edit_tags" id="edit_tags" value="{$item.tags}" />
            </p>
            
            {if $edit == 'project'}
            <input type="button" onClick="insert_post('{$project.slug}');" value="insert post" />
            {/if}

            <ul id="content_tabs">
                <li><a id="tab_content" href="javascript:tab('content');" class="selected">content</a></li>            
                {if $edit == 'project'}
                    <li><a id="tab_css"     href="javascript:tab('css');">css</a></li>
                    <li><a id="tab_script"  href="javascript:tab('script');">script</a></li>
                    <li><a id="tab_misc"    href="javascript:tab('misc');">settings</a></li>
                {elseif $edit == 'post'}
                    <li><a id="tab_comments"     href="javascript:tab('comments');">comments</a></li>
                {/if}
            </ul>     
            
            <div id="content_textareas">
                <textarea id="edit_content" name="edit_content">{$item.content|@escape}</textarea>
                {if $edit == 'project'}
                    <textarea id="edit_css"     name="edit_css"    style="display:none;">{$item.css|@escape}</textarea>
                    <textarea id="edit_script"  name="edit_script" style="display:none;">{$item.script|@escape}</textarea>
                {/if}
            </div>
            
            {if $edit == 'project'}            
                <div id="edit_misc" class="edit_pane" style="display:none;">
                    <label>Thumbnail<label><br/>
                    <iframe src="{$base_dir}/upload.php?slug={$item.slug}&type=thumb" class="uploadframe"></iframe>
                </div>
            {elseif $edit == 'post'}
                <div id="edit_comments" class="edit_pane" style="display:none;">
                
                    <label>Comments are </label> 
                    <select id="edit_comments_enabled" name="edit_comments_enabled">
                        <option value="yes" selected>enabled</option>
                        <option value="no">disabled</option>                        
                    </select>
                    
                    <br/><br/>
                                        
                    Comments
                </div>
            {/if}
                        
            <br/>
            
            <input type="hidden" id="item_type" value="{$edit}" />
            <input type="hidden" id="edit_id" value="{$item.id}">
            <input value="Update" type="submit" style="float:right;">
            <input type="button" value="delete"  title="delete" id="delete" onClick="delete_item();" />
            
        </form>
    </div>

{/if}
