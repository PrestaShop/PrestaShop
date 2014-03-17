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

{block name="field"}
	{if $field['type'] == 'theme'}
		{if $field['can_display_themes']}
			<div class="col-lg-12">
				<div class="row">
				{foreach $field.themes as $theme}
					<div class="col-lg-2 {if $theme->id == $field['id_theme']}select_theme_choice{/if}" onclick="$(this).find('input').attr('checked', true); $('.select_theme').removeClass('select_theme_choice'); $(this).toggleClass('select_theme_choice');">
						<div class="radio">
							<label>
								<input type="radio" name="id_theme" value="{$theme->id}" {if $theme->id == $field['id_theme']}checked="checked"{/if} /> {$theme->name}
							</label>
						</div>
						<div class="theme_container">
							<img class="img-thumbnail" src="../themes/{$theme->directory}/preview.jpg" alt="{$theme->directory}" />
						</div>
					</div>
				{/foreach}
				</div>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
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
