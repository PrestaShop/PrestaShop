<div class="{$div_style|default:"btn-group dropdown"}">
    {if isset($default_item)}
        <a href="{$default_item.href|default:"#"}" title="{$default_item.title|default:$default_item.label|default:""}" class="btn btn-default" {if isset($disabled) && $disabled == true}disabled="disabled"{/if}>
            <i class="{$default_item.icon|default:"icon-pencil"}"></i>&nbsp;{$default_item.label|default:"No label"}
        </a>
    {/if}
    <button {if $button_id}id="{$button_id}"{/if} class="btn btn-default dropdown-toggle" {if isset($disabled) && $disabled == true}disabled="disabled"{/if} data-toggle="dropdown">
        {$menu_label|default:""}&nbsp;
        <i class="{$menu_icon|default:"icon-caret-down"}"></i>&nbsp;
    </button>
    <ul class="dropdown-menu">
        {foreach from=$items key=key item=entry}
            {if isset($entry.divider) && $entry.divider==true}
                <li class="divider"></li>
            {else}
                <li>
                    <a href="{$entry.href|default:"#"}" {if isset($entry.onclick)}onclick="{$entry.onclick}"{/if}>
                        <i class="{$entry.icon|default:""}"></i>
                        {$entry.label|default:"No label"}
                    </a>
                </li>
            {/if}
        {/foreach}
    </ul>
</div>
