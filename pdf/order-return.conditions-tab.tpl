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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<table class="product" width="100%" cellpadding="4" cellspacing="0">
	<tr>
		<th class="header small left" valign="middle">{l s='If the following conditions are not met, we reserve the right to refuse your package and/or refund:' d='Shop.Pdf' pdf='true'}</th>
	</tr>
	<tr>
		<td class="center small white">
			<ul class="left">
				<li>{l s='Please include this return reference on your return package:' d='Shop.Pdf' pdf='true'} {$order_return->id}</li>
				<li>{l s='All products must be returned in their original package and condition, unused and without damage.' d='Shop.Pdf' pdf='true'}</li>
				<li>{l s='Please print out this document and slip it into your package.' d='Shop.Pdf' pdf='true'}</li>
				<li>{l s='The package should be sent to the following address:' d='Shop.Pdf' pdf='true'}</li>
			</ul>
			<span style="margin-left: 20px;">{$shop_address}</span>
		</td>
	</tr>
</table>
<br/>
{l s='Upon receiving your package, we will notify you by e-mail. We will then begin processing the refund, if applicable. Let us know if you have any questions' d='Shop.Pdf' pdf='true'}
