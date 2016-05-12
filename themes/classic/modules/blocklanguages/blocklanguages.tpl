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
<div class="language-selector dropdown js-dropdown">
  <span class="expand-more" data-toggle="dropdown">{$current_language.name_simple}</span>
  <a data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="material-icons expand-more">&#xE5C5;</i>
  </a>
  <ul class="dropdown-menu" aria-labelledby="dLabel">
    {foreach from=$languages item=language}
      <li {if $language.id_lang == $current_language.id_lang} class="current" {/if}>
        <a href="{$link->getLanguageLink($language.id_lang)}" class="dropdown-item">{$language.name_simple}</a>
      </li>
    {/foreach}
  </ul>
</div>
