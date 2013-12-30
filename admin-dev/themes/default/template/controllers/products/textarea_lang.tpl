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

{foreach from=$languages item=language}
{if $languages|count > 1}
<div class="translatable-field lang-{$language.id_lang}">
	<div class="col-lg-9">
{/if}
		<textarea
			id="{$input_name}_{$language.id_lang}"
			name="{$input_name}_{$language.id_lang}"
			class="{if isset($autosize_js)}textarea-autosize{else}autoload_rte{/if}">{if isset($input_value[$language.id_lang])}{$input_value[$language.id_lang]|htmlentitiesUTF8}{/if}</textarea>
{if $languages|count > 1}
	</div>
	<div class="col-lg-2">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			{$language.iso_code}
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			{foreach from=$languages item=language}
			<li><a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a></li>
			{/foreach}
		</ul>
	</div>
</div>
{/if}
<span class="counter" max="{if isset($max)}{$max}{else}none{/if}"></span>
{/foreach}

{if isset($autosize_js)}
<script src="{$autosize_js}" type="text/javascript"></script>
{/if}
<script type="text/javascript">
	var iso = '{$iso_tiny_mce}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';

	{if isset($autosize_js)}
	$(".textarea-autosize").autosize();
	{/if}
</script>