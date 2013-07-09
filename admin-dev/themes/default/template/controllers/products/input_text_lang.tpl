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

{foreach from=$languages item=language}
	<div class="input-group col-lg-12 translatable-field lang-{$language.id_lang}">
		<input id="{$input_name}_{$language.id_lang}" type="text"
		name="{$input_name}_{$language.id_lang}"
		value="{$input_value[$language.id_lang]|htmlentitiesUTF8|default:''}"
		onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();">
		<div class="input-group-btn">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<img src="{$base_url}/img/l/{$language.id_lang|intval}.jpg" alt="">
				{$language.iso_code}
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				{foreach from=$languages item=language}
				<li><a href="javascript:hideOtherLanguage({$language.id_lang});"><img src="{$base_url}/img/l/{$language.id_lang|intval}.jpg" alt=""> {$language.iso_code}</a></li>
				{/foreach}
			</ul>
		</div>
	</div>
{/foreach}