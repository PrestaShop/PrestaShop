{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/options/options.tpl"}

{block name="input"}
	{if $field['type'] == 'theme'}
		{if $field['can_display_themes']}
			<div class="col-lg-12">
				<a class="btn btn-link pull-right" href="{$field['addons_link']}"><i class="icon icon-share-alt"></i> Visit Theme store</a>
			</div>
			<div class="col-lg-12">
				<div class="row">
				{if $field.themes|count > 1}
					{foreach $field.themes as $theme}
						<div class="col-lg-3">
							<div class="theme_container">
								<h4>{$theme.name}</h4>
								<div class="thumbnail-wrapper" style="display: inline;">
									<div class="action-wrapper" style="position: absolute;display:none;">
										<div class="action-overlay" style="position: absolute; width: 100%; height: 100%; background:black; opacity: 0.7;"></div>
										<div class="action-buttons" style="position: absolute;  width: 100%; top:45%; text-align:center;">
											<div class="btn-group">
												<a href="{$link->getAdminLink('AdminThemes')|addslashes}&submitOptionstheme&id_theme={$theme.id}" class="btn btn-default">
													<i class="icon-check"></i> {l s='Use this theme'}
												</a>
												<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<i class="icon-caret-down"></i>&nbsp;
												</button>
												<ul class="dropdown-menu">
													<li>
														<a href="{$link->getAdminLink('AdminThemes')|addslashes}&deletetheme&id_theme={$theme.id}" title="Delete" class="delete">
															<i class="icon-trash"></i> {l s='Delete'}
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
									<img class="img-thumbnail" src="{$theme.preview}" alt="{$theme.name}" />
								</div>

							</div>
						</div>
					{/foreach}
				{else}
					<div style="text-align:center;">
						{l s='You currently have no other themes. Choose a new theme:'}
						<br />	
						<a class="btn btn-default" href="{$field['addons_link']}">
							<i class='icon icon-folder-open'></i>
							{l s='Visit Theme store'}
						</a>
					</div>
				{/if}
				</div>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}


{block name="footer"}

	{if isset($categoryData['after_tabs'])}
		{assign var=cur_theme value=$categoryData['after_tabs']['cur_theme']}
		<div class="row" style="margin-top: 64px;">
			<div class="col-md-2">
				<h2>{l s='Your theme'}</h2>
			</div>
			<div class="col-md-3">
				<a href="{$base_url}">
					<img src="../themes/{$cur_theme.theme_directory}/preview.jpg">
				</a>
			</div>
			<div class="col-md-7">
				<h2>{$cur_theme.theme_name} <small>version {$cur_theme.theme_version}</small></h2>
				<p>
					Designed by {$cur_theme.author_name}
					{if isset($cur_theme.author_url) && $cur_theme.author_url != ''}
					 | <a href="{$cur_theme.author_url}">{$cur_theme.author_url}</a>
					{/if}
					{if isset($cur_theme.author_email) && $cur_theme.author_email != ''}
					 | <a href="mailto:{$cur_theme.author_email}">{$cur_theme.author_email}</a>
					{/if}
				</p>

				{if $cur_theme.tc}
				<hr style="margin 24px 0;">
				<div class="row">
					<div class="col-md-8 col-sm-8">
						<h4>{l s='Customize your theme'}</h4>
						<p>{l s='Customize the main elements (sliders, banners, colors,...) that are specific to your theme'}</p>
					</div>
					<div class="col-md-4 col-sm-4" style="text-align:center;">
						<h4>&nbsp;</h4>
						<a class="btn btn-default">Theme Configurator</a>
					</div>
				</div>
				{/if}

				<hr style="margin 24px 0;">
				<div class="row">
					<div class="col-md-8 col-sm-8">
						<h4>{l s='Configure your theme'}</h4>
						<p>{l s='Configure advanced settings such as the number of columns you want for each page (mostly for developers)'}</p>
					</div>
					<div class="col-md-4 col-sm-4" style="text-align:center;">
						<h4>&nbsp;</h4>
						<a href="{$link->getAdminLink('AdminThemes')|addslashes}&updatetheme&id_theme={$cur_theme.theme_id}" class="btn btn-default">Advanced settings</a>
					</div>
				</div>
			</div>
		</div>

	{/if}

	{$smarty.block.parent}

{/block}


{block name="after"}
	<div class="panel clearfix" id="prestastore-content"></div>
	<script type="text/javascript">
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: 'ajax-tab.php?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "html",
			data: {
				tab: 'AdminThemes',
				token: '{$token}',
				ajax: '1',
				action:'getAddonsThemes',
				page:'themes'
			},
			success: function(htmlData) {
				$("#prestastore-content").html("<h3><i class='icon-picture-o'></i> {l s='Live from PrestaShop Addons!'}</h3>"+htmlData);
			}
		});
	</script>
{/block}