{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
{if isset($updateOrderStatus_mode) && $updateOrderStatus_mode}
	<div class="panel">
		<div class="panel-heading">
			{l s='Choose an order status' d='Admin.Orderscustomers.Feature'}
		</div>
		<form action="{$REQUEST_URI}" method="post">
			<div class="radio">
				<label for="id_order_state">
					<select id="id_order_state" name="id_order_state">
{foreach from=$order_statuses item=order_status_name key=id_order_state}
						<option value="{$id_order_state|intval}">{$order_status_name|escape}</option>
{/foreach}
					</select>
				</label>
			</div>
{foreach $POST as $key => $value}
	{if is_array($value)}
		{foreach $value as $val}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}[]" value="{$val|escape:'html':'UTF-8'}" />
		{/foreach}
	{elseif strtolower($key) != 'id_order_state'}
			<input type="hidden" name="{$key|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}" />

	{/if}
{/foreach}
			<div class="panel-footer">
				<button type="submit" name="cancel" class="btn btn-default">
					<i class="icon-remove"></i>
					{l s='Cancel' d='Admin.Actions'}
				</button>
				<button type="submit" class="btn btn-default" name="submitUpdateOrderStatus">
					<i class="icon-check"></i>
					{l s='Update Order Status' d='Admin.Orderscustomers.Feature'}
				</button>
			</div>
		</form>
	</div>
{/if}
{/block}
