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

<!-- Block languages module -->
{if count($languages) > 1}
	<div id="languages-block-top">
		<div id="countries">
			{foreach from=$languages key=k item=language name="languages"}
				{if $language.iso_code == $lang_iso}
					<div class="current">
						<span>{$language.name}</span>
					</div>
				{/if}
			{/foreach}
			<ul id="first-languages" class="countries_ul toogle_content">
				{foreach from=$languages key=k item=language name="languages"}
					<li {if $language.iso_code == $lang_iso}class="selected"{/if}>
					{if $language.iso_code != $lang_iso}
						{assign var=indice_lang value=$language.id_lang}
						{if isset($lang_rewrite_urls.$indice_lang)}
							<a href="{$lang_rewrite_urls.$indice_lang|escape:'html':'UTF-8'}" title="{$language.name}">
						{else}
							<a href="{$link->getLanguageLink($language.id_lang)|escape:'html':'UTF-8'}" title="{$language.name}">
						{/if}
					{/if}
							<span>{$language.name}</span>
					{if $language.iso_code != $lang_iso}
						</a>
					{/if}
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#countries .current span, #countries .countries_ul li span').each(function() {
				var h = $(this).html();
				var index = h.indexOf(' ');
					if(index == -1) {
						index = h.length;
					}
				$(this).html('<span class="firstWord">'+ h.substring(index, h.length) + '</span>' + h.substring(0, index));
			});
		}); 
	</script>
{/if}
<!-- /Block languages module -->
