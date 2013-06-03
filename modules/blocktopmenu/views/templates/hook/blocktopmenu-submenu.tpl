<ul>
	{foreach from=$links item="link"}
		<li {if $link.selected}class="sfHoverForce"{/if}>
			<a href="{$link.href}" {if isset($link.new_window) && $link.new_window}target="_blank"{/if}>
				{$link.label}
			</a>
			{if isset($link.submenu) && is_array($link.submenu) && count($link.submenu) > 0}
				{include file="$self/views/templates/hook/blocktopmenu-submenu.tpl" items=$link.submenu}
			{/if}
		</li>
	{/foreach}
</ul>