{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
$().ready(function() {
	// Click on "all shop"
	$('.input_all_shop').live('click', function() {
		var checked = $(this).attr('checked');
		$('.input_group_shop:not(:disabled)').attr('checked', checked);
		$('.input_shop:not(:disabled)').attr('checked', checked);
	});

	// Click on a group shop
	$('.input_group_shop').live('click', function() {
		$('.input_shop[value='+$(this).val()+']').attr('checked', $(this).attr('checked'));
		check_all_shop();
	});

	// Click on a shop
	$('.input_shop').live('click', function() {
		check_group_shop_status($(this).val());
		check_all_shop();
	});

	// Initialize checkbox
	$('.input_group_shop').each(function(k, v) {
		check_group_shop_status($(v).val());
		check_all_shop();
	});
});

function check_group_shop_status(id_group) {
	var groupChecked = true;
	var total = 0;
	$('.input_shop[value='+id_group+']').each(function(k, v) {
		total++;
		if (!$(v).attr('checked'))
			groupChecked = false;
	});

	if (total > 0)
		$('.input_group_shop[value='+id_group+']').attr('checked', groupChecked);
}

function check_all_shop() {
	var allChecked = true;
	$('.input_group_shop:not(:disabled)').each(function(k, v) {
		if (!$(v).attr('checked'))
			allChecked = false;
		});
	$('.input_all_shop').attr('checked', allChecked);
}
</script>

<div class="assoShop">
	<table class="table" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<th>{if $input.type == 'group_shop'}{l s='Group shop'}{else}{l s='Shop'}{/if}</th>
		</tr>
		<tr {if $input.type == 'group_shop'}class="alt_row"{/if}>
			<td>
				<label class="t"><input class="input_all_shop" type="checkbox" /> <b>{if $input.type == 'group_shop'}{l s='All group shops'}{else}{l s='All shops'}{/if}</b></label>
			</td>
		</tr>
		{foreach $input.values as $groupID => $groupData}
			{if ($input.type == 'group_shop' && isset($fields_value.shop[$groupID]))}
				{assign var=groupChecked value=true}
			{else}
				{assign var=groupChecked value=false}
			{/if}
			<tr {if $input.type == 'shop'}class="alt_row"{/if}>
				<td>
					<img style="vertical-align:middle;" alt="" src="../img/admin/lv2_b.gif" />
					<label class="t">
						<input class="input_group_shop"
							type="checkbox"
							name="checkBoxGroupShopAsso_{$table}[{$form_id}][{$groupID}]"
							value="{$groupID}"
							{if $groupChecked} checked="checked"{/if} />
						<b>{l s='Group:'} {$groupData['name']}</b>
					</label>
				</td>
			</tr>
	
			{if $input.type == 'shop'}
				{assign var=j value=0}
				{foreach $groupData['shops'] as $shopID => $shopData}
					{if (isset($fields_value.shop[$shopID]))}
						{assign var=checked value=true}
					{else}
						{assign var=checked value=false}
					{/if}
					<tr>
						<td>
							<img style="vertical-align:middle;" alt="" src="../img/admin/lv3_{if $j < count($groupData['shops']) - 1}b{else}f{/if}.png" />
							<label class="child">
								<input class="input_shop"
									type="checkbox"
									value="{$groupID}"
									name="checkBoxShopAsso_{$table}[{$form_id}][{$shopID}]"
									id="checkedBox_{$shopID}"
									{if $checked} checked="checked"{/if} />
								{$shopData['name']}
							</label>
						</td>
					</tr>
					{assign var=j value=$j+1}
				{/foreach}
			{/if}
		{/foreach}
	</table>
</div>