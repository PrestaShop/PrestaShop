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

<div class="currency-selector drop-down js-drop-down col-lg-3 col-md-3">
  <span class="expand-more">{$current_currency.iso_code} {$current_currency.sign}</span>
  <i class="material-icons expand-more">expand_more</i>
  <ul>
    {foreach from=$currencies item=currency}
      <li {if $currency.current} class="current" {/if}>
        <a rel="nofollow" href="{$currency.url}">{$currency.iso_code} {$currency.sign}</a>
      </li>
    {/foreach}
  </ul>
</div>
