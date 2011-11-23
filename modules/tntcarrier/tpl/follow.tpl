{*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8088 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<br/>
{if isset($follow)}
{foreach from="$follow" item="f"}
<table class="std" style="text-align:left">
	<thead>
	<tr>
		<th class="first_item" colspan="2">{l s='Package Information'}</th>
		<th class="last_item" colspan="2"></th>
	</tr>
	<tr class="item">
		<td class="bold">{l s='Shipping number'}</td>
		<td>{$f.number}</td>
		<td class="bold">{l s='Shipping status'}</td>
		<td>{$f.status}</td>
	</tr>
	<tr class="item">
		<td class="bold">{l s='Reference'}</td>
		<td>{$f.reference}</td>
		<td class="bold">{l s='Service'}</td>
		<td>{$f.service}</td>
	<tr>
	<tr class="item">
		<td class="bold">{l s='Weight'}</td>
		<td>{$f.weight}</td>
		<td></td>
		<td></td>
	</tr>
	<tr class="item" style="border-top:1px solid black">
		<td class="bold">{l s='Event'}</th>
		<td class="bold">{l s='Date'}</th>
		<td class="bold">{l s='Hours'}</th>
		<td class="bold">{l s='Name Center'}</th>
	</tr>
	{if $f.request}
	<tr class="item">
		<td>{l s='Package on request'}</td>
		<td>{$f.requestDate|date_format:$config.date}</td>
		<td>{$f.requestDate|date_format:$config.time}</td>
		<td></td>
	</tr>
	{/if}
	{if $f.process}
	<tr class="item">
		<td>{l s='Package on its way'}</td>
		<td>{$f.process_date|date_format:$config.date}</td>
		<td>{$f.process_date|date_format:$config.time}</td>
		<td>{$f.process_center}</td>
	<tr>
	{/if}
	{if $f.delivery_departure}
	<tr class="item">
		<td>{l s='Deposit departure'}</td>
		<td>{$f.delivery_departure_date|date_format:$config.date}</td>
		<td>{$f.delivery_departure_date|date_format:$config.time}</td>
		<td>{$f.delivery_departure_center}</td>
	</tr>
	{/if}
	{if $f.delivery}
	<tr class="item">
		<td>{l s='driver presentation'}</td>
		<td>{$f.delivery_date|date_format:$config.date}</td>
		<td>{$f.delivery_date|date_format:$config.time}</td>
		<td></td>
	</tr>
	{/if}
	<tr class="item">
		<td class="bold">deposit status</td>
		<td colspan="3">
			{foreach $f.long_status as $line}
			{$line}<br/>
			{/foreach}
			{if $f.linkPicture != ''}
			<a href="{$f.linkPicture}" target="_blank">{l s='proof signature' mod='tntcarrier'}</a>
			{/if}
		</td>
	</tr>
	</thead>
</table>
{/foreach}
{/if}

