{if isset($htmlitems.items) && $htmlitems.items}
<div id="htmlcontent_top">
    <ul class="htmlcontent-home clearfix">
        {foreach name=items from=$htmlitems.items item=hItem}
        	<li class="htmlcontent-item">
            	{if $hItem.url}
                	<a href="{$hItem.url}" class="item-link"{if $hItem.target == 1} target="_blank"{/if}>
                {/if}
	            	{if $hItem.image}
	                	<img src="{$module_dir}images/{$hItem.image}" class="item-img" alt="" />
	                {/if}
	            	{if $hItem.title && $hItem.title_use == 1}
                        <h3 class="item-title">{$hItem.title}</h3>
	                {/if}
	            	{if $hItem.html}
	                	<div class="item-html">
                        	{$hItem.html} <i class="icon-double-angle-right"></i>                            
                        </div>
	                {/if}
            	{if $hItem.url}
                	</a>
                {/if}
            </li>
        {/foreach}
    </ul>
</div>
{/if}