{**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
	{if !$shop_context}
		<div class="alert alert-warning">{l s='You have more than one shop and must select one to configure payment.' d='Admin.Payment.Notification'}</div>
	{else}
		<div class="alert alert-info">
			{l s='This is where you decide what payment modules are available for different variations like your customers\' currency, group, and country.' d='Admin.Payment.Help'}
			<br />
			{l s='A check mark indicates you want the payment module available.' d='Admin.Payment.Help'}
			{l s='If it is not checked then this means that the payment module is disabled.' d='Admin.Payment.Help'}
			<br />
			{l s='Please make sure to click Save for each section.' d='Admin.Payment.Help'}
		</div>
		{if $display_restrictions}
			{foreach $lists as $list}
				{include file='controllers/payment_preferences/restrictions.tpl'}
			{/foreach}
		{else}
			<div class="alert alert-warning">{l s='No payment module installed' d='Admin.Payment.Notification'}</div>
		{/if}
	{/if}
{/block}
