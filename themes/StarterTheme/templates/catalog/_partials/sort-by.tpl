<div class="products-sort-by">
    <span>{l s='Sort by:'}</span>
    <ul>
        {foreach from=$options item=option}
            <li class="{['current' => $option.current]|classnames}"><a rel="nofollow" href="{$option.url}">{$option.label}</a></li>
        {/foreach}
    </ul>
</div>
