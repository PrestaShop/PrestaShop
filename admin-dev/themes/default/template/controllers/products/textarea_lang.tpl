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

<div class="translatable">
{foreach from=$languages item=language}
<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display:none;{/if}float: left;">
	<textarea cols="100" rows="10" id="{$input_name}_{$language.id_lang}" 
		name="{$input_name}_{$language.id_lang}" 
		class="autoload_rte" >{if isset($input_value[$language.id_lang])}{$input_value[$language.id_lang]|htmlentitiesUTF8}{/if}</textarea>
	<span class="counter" max="{if isset($max)}{$max}{else}none{/if}"></span>
	<span class="hint">{$hint|default:''}<span class="hint-pointer">&nbsp;</span></span>
</div>
{/foreach}
</div>
<script type="text/javascript">
	var iso = '{$iso_tiny_mce}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';
</script>
