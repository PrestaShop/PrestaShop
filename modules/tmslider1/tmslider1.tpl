{if $page_name == 'index'}
<div id="tmslider1">
<ul>
	{foreach from=$xml->link item=home_link name=links}
	<li class="slide{$smarty.foreach.links.iteration}">
		<div>{$home_link->desc}</div>
		<a href="{$home_link->url}"><img src="{$this_path}{$home_link->img}" alt="" /></a>
	</li>
	{/foreach}
</ul>
</div>
<div class="clear"></div>
{/if}