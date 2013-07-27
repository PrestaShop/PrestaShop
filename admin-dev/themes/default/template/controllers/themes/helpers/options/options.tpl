{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/options/options.tpl"}

{block name="field"}
	{if $field['type'] == 'theme'}
		{if $field['can_display_themes']}
			{foreach $field.themes as $theme}
				<div class="select_theme {if $theme->id == $field['id_theme']}select_theme_choice{/if}" onclick="$(this).find('input').attr('checked', true); $('.select_theme').removeClass('select_theme_choice'); $(this).toggleClass('select_theme_choice');">
					{$theme->name}<br />
					<img src="../themes/{$theme->directory}/preview.jpg" alt="{$theme->directory}" /><br />
					<input type="radio" name="id_theme" value="{$theme->id}" {if $theme->id == $field['id_theme']}checked="checked"{/if} />
				</div>
			{/foreach}
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="after"}
	<br/><br/>
	<fieldset id="prestastore-content" class="width3"></fieldset>
	<script type="text/javascript">
		$.post(
			"ajax-tab.php",
			{
				tab: 'AdminThemes',
				token: '{$token}',
				ajax: '1',
				action:'getAddonsThemes',
				page:'themes'
			}, function(a){
				$("#prestastore-content").html("<legend><img src='../img/admin/prestastore.gif' class='middle' />{l s='Live from PrestaShop Addons!'}</legend>"+a);
			});
	</script>
{/block}
