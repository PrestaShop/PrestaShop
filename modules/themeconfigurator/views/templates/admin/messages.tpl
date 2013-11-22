<div id="{$id}-response" {if !isset($text)}style="display:none;"{/if} class="message alert alert-{if isset($class) && $class=='error'}danger{else}success{/if}">
	<div>{if isset($text)}{$text}{/if}</div>
</div>