{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

{extends file="helpers/list/list_footer.tpl"}
	{block name="after"}
		{if is_null($modules_list)}
			<div class="panel">
				<h3>{l s="Use one of our recommended carrier modules" d='Admin.Shipping.Feature'}</h3>
				<p>{l s="It seems there are no recommended carriers for your country." d='Admin.Shipping.Feature'}</p>
				<p><a href="https://www.prestashop.com/en/contact-us">{l s="Do you think there should be one? Let us know!" d='Admin.Shipping.Feature'}</a></p>
			</div>
		{else}
			{$modules_list}
		{/if}
	{/block}
