{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="row">
	<div class="col-xs-12 {if !$hide_left_column && !$hide_right_column}col-md-6{else if !$hide_left_column || !$hide_right_column}col-md-8{else}col-md-12{/if}">
        <p class="payment_module">
            <a class="cheque" href="{$link->getModuleLink('cheque', 'payment', [], true)|escape:'html':'UTF-8'}" title="{l s='Pay by check.' mod='cheque'}">
                {l s='Pay by check' mod='cheque'} <span>{l s='(order processing will be longer)' mod='cheque'}</span>
            </a>
        </p>
    </div>
</div>
