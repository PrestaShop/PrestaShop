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
<div id="{$id|escape}" class="col-lg-3 box-stats {$color|escape}" >
	<i class="{$icon|escape}"></i>
	<span class="title">{$title|escape}<br /><small>{$subtitle|escape}</small></span>
	<span class="value">{$value|escape}</span>
</div>
{if $source != ''}
<script>
$.ajax({
	url: '{$source|addslashes}' + '&rand=' + new Date().getTime(),
	dataType: 'json',
	type: 'GET',
	cache: false,
	headers: { 'cache-control': 'no-cache' },
	success: function(jsonData){
		if (!jsonData.has_errors)
			$('#{$id|addslashes} .value').html(jsonData.value);
	}
});
</script>
{/if}