<ul id="project_list">
    {foreach item="item" from=$sorted_project_list}
    <li id="{$item.type}_{$item.id}" class="{$item.type}">
        {if $item.type == 'project'}
        <a href="{$item.url}/">{$item.title}</a>
        {else}
            {$item.title}
        {/if}    
    </li>            
    {/foreach}        
</ul>
