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

<br />
<fieldset style="width: 400px">
			<legend><img src="../img/admin/tab-customers.gif" />{$payment_name}</legend>
			{if $error}
				<span style="color: red; font-weight: bold;">{$error}</span>
			{/if}
			
			<p style="font-weight: bold;">{l s='Payment has not been accepted yet:' mod='paysafecard'}</p>
			<p>
			<form action="{$action}" method="POST">
				<input type="text" name="ps_amount" size="8" value="{$amount}" />{$currency} 
				<input type="submit" class="button" name="acceptPayment" value="{l s='Accept Payment' mod='paysafecard'}" />
				<input type="submit" class="button" name="releasePayment" value="{l s='Release amount' mod='paysafecard'}" />				
			</form>
			</p>
</fieldset>
			
