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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<p class="payment_module">
	{if $cart->getOrderTotal() < 2}
		<a href="">
			<img src="{$this_path}hipay.png" alt="{l s='Pay with Hipay' mod='hipay'}" />
			{l s='Minimum amount required in order to pay with Hipay:' mod='hipay'} {convertPrice price=2}
		</a>
	{else}
	<a href="{$this_path_ssl}redirect.php" title="{l s='Pay with Hipay' mod='hipay'}">
		<img src="{$this_path}hipay.png" alt="{l s='Pay with Hipay' mod='hipay'}" />
		{l s='Pay with Hipay' mod='hipay'} {if !$hipay_prod}{l s='(sandbox / test mode)' mod='hipay'}{/if}
	</a>
	{/if}
</p>