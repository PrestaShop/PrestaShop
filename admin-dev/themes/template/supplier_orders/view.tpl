{*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helper/view/view.tpl"}

{block name="override_tpl"}

	<div>
		<h2>{l s='Supplier Order'} #{$supplier_order_reference}</h2>
	</div>
	
	<div style="margin-top: 20px;">
		<fieldset>
			<legend>{l s='Order'} #{$supplier_order_reference}</legend>
			<table style="width: 400px;" classe="table">
				<tr>
					<td>{l s='Employee:'}</td>
					<td>{$supplier_order_employee}</td>
				</tr>
				<tr>
					<td>{l s='Last update:'}</td>
					<td>{$supplier_order_last_update}</td>
				</tr>
				<tr>
					<td>{l s='Delivery expected:'}</td>
					<td>{$supplier_order_expected}</td>
				</tr>
			</table>
		</fieldset>
	</div>
	
	<div style="margin-top: 20px;">
		<fieldset>
			<legend>{l s='Products'}</legend>
			{$supplier_order_detail_content}
		</fieldset>
	</div>
	
	<div style="margin-top: 20px;">
		<fieldset>
			<legend>{l s='Summary'}</legend>
			<table style="width: 400px;" classe="table">
				<tr>
					<th>{l s='Designation'}</th>
					<th width='60px'>{l s='Value'}</th>
				</tr>
				<tr>
					<td bgcolor="#000000"></td>
					<td bgcolor="#000000"></td>
				</tr>
				<tr>
					<td>{l s='Total TE'}</td>
					<td>{$supplier_order_total_te}</td>
				</tr>
				<tr>
					<td>{l s='Discount'}</td>
					<td>{$supplier_order_discount_value_te}</td>
				</tr>
				<tr>
					<td>{l s='Total with discount TE'}</td>
					<td>{$supplier_order_total_with_discount_te}</td>
				</tr>
				<tr>
					<td bgcolor="#000000"></td>
					<td bgcolor="#000000"></td>
				</tr>
				<tr>
					<td>{l s='Total Tax'}</td>
					<td>{$supplier_order_total_tax}</td>
				</tr>
				<tr>
					<td>{l s='Total TI'}</td>
					<td>{$supplier_order_total_ti}</td>
				</tr>
				<tr>
					<td bgcolor="#000000"></td>
					<td bgcolor="#000000"></td>
				</tr>
				<tr>
					<td>{l s='TOTAL TO PAY'}</td>
					<td>{$supplier_order_total_ti}</td>
				</tr>
			</table>
		</fieldset>
	</div>
	
	<div style="margin-top: 20px;">
		<a href="{$current}&token={$token}">
			<img src="../img/admin/arrow2.gif" />{l s='Back to supplier orders'}
		</a>
	</div>

{/block}