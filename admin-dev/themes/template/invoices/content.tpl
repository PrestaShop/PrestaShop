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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h2>{l s='Print PDF invoices'}</h2>
<fieldset style="float:left;width:300px"><legend><img src="../img/admin/pdf.gif" alt="" /> {l s='By date'}</legend>
	<form action="{$url_post}" method="post">
		<label style="width:90px">{l s='From:'} </label>
		<div class="margin-form" style="padding-left:100px">
			<input type="text" size="4" maxlength="10" name="date_from" value="{$date}" style="width: 120px;" /> <sup>*</sup>
			<p class="clear">{l s='Format: 2007-12-31 (inclusive)'}</p>
		</div>
		<label style="width:90px">{l s='To:'} </label>
		<div class="margin-form" style="padding-left:100px">
			<input type="text" size="4" maxlength="10" name="date_to" value="{$date}" style="width: 120px;" /> <sup>*</sup>
			<p class="clear">{l s='Format: 2008-12-31 (inclusive)'}</p>
		</div>
		<div class="margin-form" style="padding-left:100px">
			<input type="submit" value="{l s='Generate PDF file'}" name="submitPrint" class="button" />
		</div>
		<div class="small"><sup>*</sup> {l s='Required fields'}</div>
	</form>
</fieldset>
<fieldset style="float:left;width: 500px;margin-left:10px"><legend><img src="../img/admin/pdf.gif" alt="" /> {l s='By statuses'}</legend>
	<form action="{$url_post}" method="post">
		<label style="width:90px">{l s='Statuses'} :</label>
		<div class="margin-form" style="padding-left:100px">
			<ul>
				{foreach $statuses as $status}
					<li style="list-style: none;">
						<input type="checkbox" name="id_order_state[]" value="{$status['id_order_state']|intval}" id="id_order_state_{$status['id_order_state']|intval}">
						<label for="id_order_state_{$status['id_order_state']|intval}" style="float:none;{if !(isset($statusStats[$status['id_order_state']]) && $statusStats[$status['id_order_state']])}font-weight:normal;{/if}padding:0;text-align:left;width:100%;color:#000">
							<img src="../img/admin/charged_{if $status['invoice']}ok{else}ko{/if}.gif" alt="" />
							{$status['name']} ({if isset($statusStats[$status['id_order_state']]) && $statusStats[$status['id_order_state']]}{$statusStats[$status['id_order_state']]}{else}0{/if})
						</label>
					</li>
				{/foreach}
			</ul>
			<p class="clear">{l s='You can also export orders which have not been charged yet.'}(<img src="../img/admin/charged_ko.gif" alt="" />)</p>
		</div>
		<div class="margin-form">
			<input type="submit" value="{l s='Generate PDF file'}" name="submitPrint2" class="button" />
		</div>
	</form>
</fieldset>
<div class="clear">&nbsp;</div>

{$content}
