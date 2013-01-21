<!-- Block RSS module-->
<div id="rss_block_left" class="block">
	<h4>{$title}</h4>
	<div class="block_content">
		{if $rss_links}
			<ul class="bullet">
				{foreach from=$rss_links item='rss_link'}
					<li><a href="{$rss_link.url}">{$rss_link.title}</a></li>
				{/foreach}
			</ul>
		{else}
			<p>{l s='No RSS feed added' mod='blockrss'}</p>
		{/if}
	</div>
</div>
<!-- /Block RSS module-->
