{if !isset($page_title) && isset($meta_title) && $meta_title != $shop_name}
	{assign var='page_title' value=$meta_title|escape:'htmlall':'UTF-8'}
{/if}
{if isset($page_title)}
	<div data-role="header" class="clearfix navbartop" data-position="inline">
		<h1>{$page_title}</h1>
	</div><!-- /navbartop -->
{/if}