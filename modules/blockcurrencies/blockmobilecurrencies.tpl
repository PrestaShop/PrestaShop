{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block Mobile currencies module -->

<div class="ui-block-a">
	<div data-role="fieldcontain">
		<form id="setCurrency" action="{$request_uri}" method="post">
			<select onchange="javascript:setCurrency(this.value)" name="select-choice-1" id="select-choice-1">
				{foreach from=$currencies key=k item=f_currency}
					<option value="{$f_currency.id_currency}" {if $cookie->id_currency == $f_currency.id_currency}selected="selected"{/if}>{$f_currency.sign} {$f_currency.name}</option>
				{/foreach}
			</select>
		</form>
	</div>
</div>

<!-- /Block Mobile currencies module -->