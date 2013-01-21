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
*  @version  Release: $Revision: 11256 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<br />
<p>
	<a class="button" href="#" onclick="if ($('.requiredFieldsParameters:visible').length == 0) $('.requiredFieldsParameters').slideDown('slow'); else $('.requiredFieldsParameters').slideUp('slow'); return false;"><img src="../img/admin/duplicate.gif" alt="" /> {l s='Set required fields for this section'}</a>
</p>
<fieldset style="display:none" class="width1 requiredFieldsParameters">
	<legend>{l s='Required Fields'}</legend>
	<form name="updateFields" action="{$current}&submitFields=1&token={$token}" method="post">
		<p>
			<b>{l s='Select the fields you would like to be required for this section.'}</b><br />
			<table cellspacing="0" cellpadding="0" class="table width1 clear">
				<thead>
					<tr>
						<th><input type="checkbox" onclick="checkDelBoxes(this.form, 'fieldsBox[]', this.checked)" class="noborder" name="checkme"></th>
						<th>{l s='Field Name'}</th>
					</tr>
				</thead>
				<tbody>
				{foreach $table_fields as $field}
					{if !in_array($field, $required_class_fields)}
						<tr class="{if $irow++ % 2}alt_row{/if}">
							<td class="noborder"><input type="checkbox" name="fieldsBox[]" value="{$field}" {if in_array($field, $required_fields)} checked="checked"{/if} /></td>
							<td>{$field}</td>
						</tr>
					{/if}
				{/foreach}
				</tbody>
			</table><br />
			<center>
				<input style="margin-left:15px;" class="button" type="submit" value="{l s='Save'}" name="submitFields" />
			</center>
		</p>
	</form>
</fieldset>
