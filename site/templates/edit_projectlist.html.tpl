

<h1>edit list order</h1>
<ul id="project_list">
    {foreach item="item" from=$sorted_project_list}
    <li id="{$item.type}_{$item.id}" class="{$item.type}{if $item.hidden == 1} admin_object hidden{/if}">
        {if $request.user}
            <div class="admin_object handle"></div>
            {if $item.type == 'spacer'}
            <input type="button" value="x" class="admin_object spacer_del"/>
            {/if}
        {/if}
        {if $item.type == 'project' || $item.type == 'post'}
            <strong>{$item.title}</strong>
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
