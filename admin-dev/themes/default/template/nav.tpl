<div class="bootstrap">
	<nav id="{if $employee->bo_menu}nav-sidebar{else}nav-topbar{/if}" role="navigation">
	<ul class="menu">
		{foreach $tabs as $level_1}

			{if $level_1.active}

				{* Dashboard exception *}

				{if $level_1.class_name == 'AdminDashboard'}
					<li class="maintab {if $level_1.current}active{/if}" id="tab-{$level_1.class_name}">
						<a href="{if $level_1.sub_tabs|@count && isset($level_1.sub_tabs[0].href)}{$level_1.sub_tabs[0].href|escape:'html':'UTF-8'}{else}{$level_1.href|escape:'html':'UTF-8'}{/if}" class="title" >
							<i class="material-icons">{$level_1.icon}</i>
							<span>{if $level_1.name eq ''}{$level_1.class_name|escape:'html':'UTF-8'}{else}{$level_1.name|escape:'html':'UTF-8'}{/if}</span>
						</a>
					</li>
				{else}

					<li class="{if $level_1.current}active{/if}" id="tab-{$level_1.class_name}">
						<div class="categorytab">
							<div class="line"></div>
							<span>{if $level_1.name eq ''}{$level_1.class_name|escape:'html':'UTF-8'}{else}{$level_1.name|escape:'html':'UTF-8'}{/if}</span>
						</div>
					</li>
					{if $level_1.sub_tabs|@count}
						{foreach $level_1.sub_tabs as $level_2}
							{if $level_2.active}
								<li class="maintab {if $level_2.current}active{/if} {if $level_2.sub_tabs|@count}has_submenu{/if}" id="subtab-{$level_2.class_name|escape:'html':'UTF-8'}" data-submenu="{$level_2.id_tab}">
									<a href="{$level_2.href|escape:'html':'UTF-8'}" class="title {if $level_2.sub_tabs|@count}has_submenu{/if}">
										<i class="material-icons">{$level_2.icon}</i>
										<span>
											{if $level_2.name eq ''}{$level_2.class_name|escape:'html':'UTF-8'}{else}{$level_2.name|escape:'html':'UTF-8'}{/if}
										</span>
									</a>
									{if $level_2.sub_tabs|@count}
										<ul class="submenu">
											{foreach $level_2.sub_tabs as $level_3}
												{if $level_3.active}
													<li class="{if $level_3.current}active{/if}" id="subtab-{$level_3.class_name|escape:'html':'UTF-8'}" data-submenu="{$level_3.id_tab}">
														<a href="{$level_3.href|escape:'html':'UTF-8'}" class="title">
															{if $level_3.name eq ''}{$level_3.class_name|escape:'html':'UTF-8'}{else}{$level_3.name|escape:'html':'UTF-8'}{/if}
														</a>
													</li>
												{/if}
											{/foreach}
										</ul>
									{/if}
								</li>
							{/if}
						{/foreach}
					{/if}
				{/if}

			{/if}
		{/foreach}
	</ul>

	<span class="menu-collapse">
		<i class="icon-align-justify icon-rotate-90"></i>
	</span>

	{hook h='displayAdminNavBarBeforeEnd'}

	</nav>
</div>
