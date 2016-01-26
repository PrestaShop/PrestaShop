{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="block_myaccount_infos" class="col-md-2">
	<h4 class="myaccount-title"><a class="_gray-dark text-uppercase" href="{$urls.pages.my_account}" title="{l s='My account' mod='blockmyaccount'}" rel="nofollow">{l s='My account' mod='blockmyaccount'}</a></h4>
	<ul class="account-list">
        {foreach from=$my_account_urls item=my_account_url}
            <li><a href="{$my_account_url.url}" title="{$my_account_url.title}" rel="nofollow">{$my_account_url.title}</a></li>
        {/foreach}
        {hook h="displayMyAccountBlock"}
	</ul>
	<p class="logout"><a href="{$logout_url}" title="{l s='Sign out' mod='blockmyaccount'}" rel="nofollow">{l s='Sign out' mod='blockmyaccount'}</a></p>
</div>
