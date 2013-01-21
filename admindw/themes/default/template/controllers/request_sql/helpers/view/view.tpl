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
*  @version  Release: $Revision: 8897 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{if isset($view['error'])}
		<p>{l s='This query has no result.'}</p>
	{else}
		<h2>{$view['name']}</h2>
		<table cellpadding="0" cellspacing="0" class="table" id="viewRequestSql">
			<thead>
				<tr>
					{foreach $view['key'] AS $key}
						<th align="center">{$key}</th>
					{/foreach}
				</tr>
			</thead>
			<tbody>
			{foreach $view['results'] AS $result}
				<tr>
					{foreach $view['key'] AS $name}
						{if isset($view['attributes'][$name])}
							<td>{$view['attributes'][$name]}</td>
						{else}
							<td>{$result[$name]}</td>
						{/if}
					{/foreach}
				</tr>
			{/foreach}
			</tbody>
		</table>
	
		<script type="text/javascript">
			$(function(){
				var width = $('#viewRequestSql').width();
				if (width > 990){
					$('#viewRequestSql').css('display','block').css('overflow-x', 'scroll');
				}
			});
		</script>
	{/if}
{/block}

