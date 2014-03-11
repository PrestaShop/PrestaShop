<div class="bootstrap">
	<nav id="{if $employee->bo_menu}nav-sidebar{else}nav-topbar{/if}" role="navigation">
		{if !$tab}
			<div class="mainsubtablist" style="display:none"></div>
		{/if}
		<ul class="menu">
			<li class="searchtab">
				<form id="header_search" method="post" action="index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}" role="search">
					<div class="form-group">
						<input type="hidden" name="bo_search_type" id="bo_search_type" />
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<i id="search_type_icon" class="icon-search"></i>
									<i class="icon-caret-down"></i>
								</button>
								<ul id="header_search_options" class="dropdown-menu">
									<li class="search-all search-option active">
										<a href="#" data-value="0" data-placeholder="{l s='What are you looking for?'}" data-icon="icon-search">
											<i class="icon-search"></i> {l s='Everywhere'}</a>
									</li>
									<li class="divider"></li>
									<li class="search-book search-option">
										<a href="#" data-value="1" data-placeholder="{l s='Product name, SKU, reference...'}" data-icon="icon-book">
											<i class="icon-book"></i> {l s='Catalog'}
										</a>
									</li>
									<li class="search-customers-name search-option">
										<a href="#" data-value="2" data-placeholder="{l s='Email, name...'}" data-icon="icon-group">
											<i class="icon-group"></i> {l s='Customers'} {l s='by name'}
										</a>
									</li>
									<li class="search-customers-addresses search-option">
										<a href="#" data-value="6" data-placeholder="{l s='123.45.67.89'}" data-icon="icon-desktop">
											<i class="icon-desktop"></i> {l s='Customers'} {l s='by ip address'}</a>
									</li>
									<li class="search-orders search-option">
										<a href="#" data-value="3" data-placeholder="{l s='Order ID'}" data-icon="icon-credit-card">
											<i class="icon-credit-card"></i> {l s='Orders'}
										</a>
									</li>
									<li class="search-invoices search-option">
										<a href="#" data-value="4" data-placeholder="{l s='Invoice Number'}" data-icon="icon-book">
											<i class="icon-book"></i> {l s='Invoices'}
										</a>
									</li>
									<li class="search-carts search-option">
										<a href="#" data-value="5" data-placeholder="{l s='Cart ID'}" data-icon="icon-shopping-cart">
											<i class="icon-shopping-cart"></i> {l s='Carts'}
										</a>
									</li>
									<li class="search-modules search-option">
										<a href="#" data-value="7" data-placeholder="{l s='Module name'}" data-icon="icon-puzzle-piece">
											<i class="icon-puzzle-piece"></i> {l s='Modules'}
										</a>
									</li>
								</ul>
							</div>
							<a href="#" class="clear_search hide"><i class="icon-remove"></i></a>
							<input id="bo_query" name="bo_query" type="text" class="form-control" value="{$bo_query}" placeholder="{l s='Search'}" />
<!--  							<span class="input-group-btn">
								<button type="submit" id="bo_search_submit" class="btn btn-primary">
									<i class="icon-search"></i>
								</button>
							</span> -->
						</div>
					</div>

					<script>
						{if isset($search_type) && $search_type}
							$(document).ready(function() {
								$('.search-option a[data-value='+{$search_type|intval}+']').click();
							});
						{/if}
					</script>
				</form>
			</li>

			{*if count($quick_access) > 0}
				<li id="header_quick" class="maintab has_submenu">
					<a href="#" id="quick_select" class="title">
						<i class="icon-AdminFlash"></i>
						<span>{l s='Quick Access'}</span>
					</a>
					<ul class="submenu">
					{foreach $quick_access as $quick}
						<li>
							<a href="{$quick.link|escape:'html':'UTF-8'}" {if $quick.new_window} onclick="return !window.open(this.href);"{/if}>{$quick.name}</a>
						</li>
					{/foreach}
					</ul>
				</li>
			{/if*}

			{foreach $tabs as $t}
				{if $t.active}
				<li class="maintab {if $t.current}active{/if} {if $t.sub_tabs|@count}has_submenu{/if}" id="maintab{$t.id_tab}" data-submenu="{$t.id_tab}">
					<a href="{if $t.sub_tabs|@count}{$t.sub_tabs[0].href}{else}{$t.href}{/if}" class="title" >
						<i class="icon-{$t.class_name}"></i>
						<span>{if $t.name eq ''}{$t.class_name}{else}{$t.name}{/if}</span>
					</a>
					{if $t.sub_tabs|@count}
						<ul class="submenu">
						{foreach from=$t.sub_tabs item=t2}
							{if $t2.active}
							<li {if $t2.current} class="active"{/if}>
								<a href="{$t2.href|escape:'html':'UTF-8'}">
									{if $t2.name eq ''}{$t2.class_name}{else}{$t2.name|escape:'html':'UTF-8'}{/if}
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
		<span class="menu-collapse">
			<i class="icon-align-justify"></i>
		</span>
	</nav>
</div>