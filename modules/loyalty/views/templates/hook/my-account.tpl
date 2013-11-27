{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE Loyalty -->
<li class="loyalty">
	<a href="{$link->getModuleLink('loyalty', 'default', ['process' => 'summary'], true)|escape:'html'}" title="{l s='My loyalty points' mod='loyalty'}" rel="nofollow">
		<img src="{$module_template_dir}images/loyalty.gif" alt="{l s='My loyalty points' mod='loyalty'}" class="icon" /> {l s='My loyalty points' mod='loyalty'}
	</a>
</li>
<!-- END : MODULE Loyalty -->