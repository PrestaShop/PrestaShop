<nav id="nav-sidebar" data-spy="affix" data-offset-top="0" role="navigation">
{if !$tab}
	<div class="mainsubtablist" style="display:none"></div>
{/if}
	<span class="menu-collapse">
		<i class="icon-align-justify"></i>
	</span>

	<ul class="menu">
	{foreach $tabs as $t}
		{if $t.active}
		<li class="maintab {if $t.current}active{/if} {if $t.sub_tabs|@count}has_submenu{/if}" id="maintab{$t.id_tab}" data-submenu="{$t.id_tab}">
			<a href="{if $t.sub_tabs|@count}{$t.sub_tabs[0].href}{else}{$t.href}{/if}" class="title" >
				<i class="icon-{$t.class_name}"></i>
				<span class="title">{if $t.name eq ''}{$t.class_name}{else}{$t.name}{/if}</span>
			</a>
			{if $t.sub_tabs|@count}
				<ul class="submenu">
				{foreach from=$t.sub_tabs item=t2}
					{if $t2.active}
					<li {if $t2.current} class="active"{/if}>
						<a href="{$t2.href|escape:'htmlall':'UTF-8'}">
							{if $t2.name eq ''}{$t2.class_name}{else}{$t2.name|escape:'htmlall':'UTF-8'}{/if}
						</a>
					</li>
					{/if}
				{/foreach}
				</ul>
			{/if}
		</li>
		{/if}
	{/foreach}
	</ul>

</nav>