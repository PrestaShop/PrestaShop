<nav id="nav-topbar" role="navigation">
{if !$tab}
	<div class="mainsubtablist" style="display:none"></div>
{/if}
	<span class="menu-collapse">
		<i class="icon-align-justify"></i>
	</span>
	<script type="text/javascript">
		$('.menu-collapse').click(function(){
			$('body').toggleClass('page-sidebar-closed');
		});
	</script>
	{if $controller_name != 'AdminLogin'}
	<ul class="menu">
		<li class="maintab">
			<a href="{$link->getAdminLink('AdminHome')|escape:'htmlall':'UTF-8'}" class="title">
				<i class="icon-AdminDashboard"></i>
				<span class="title">{l s='Dashboard'}</span>
			</a>
		</li>
	{foreach $tabs as $t}
		{if $t.active}
		<li class="maintab {if $t.current}active{/if}" id="maintab{$t.id_tab}">
			<a href="{if count($t.sub_tabs) > 0}{$t.sub_tabs[0].href}{else}#{/if}" class="title" onclick="javascript:adminNav('#maintab{$t.id_tab}');">
				<i class="icon-{$t.class_name}"></i>
				<span class="title">{if $t.name eq ''}{$t.class_name}{else}{$t.name}{/if}</span>
			</a>
			<ul class="submenu">
			{foreach from=$t.sub_tabs item=t2}
				{if $t2.active}
				<li {if $t2.current} class="active"{/if}>
					<a href="{$t2.href|escape:'htmlall':'UTF-8'}">
						{if $t2.name eq ''}{$t2.class_name}{else}{$t2.name|escape:'htmlall':'UTF-8'}{/if}
						<i class="icon-chevron-sign-right pull-right"></i>
					</a>
				</li>
				{/if}
			{/foreach}
			</ul>
		</li>
		{/if}
	{/foreach}
	</ul>
	{/if}
</nav>