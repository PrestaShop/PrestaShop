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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<table cellpadding="0" cellspacing="0">
	<tr>
		<td style="width:50%">
			<label>{l s='Name'}</label>
			<div class="margin-form">
				<div class="translatable">
				{foreach from=$languages item=language}
					<div class="lang_{$language.id_lang}" style="display:{if $language.id_lang == $defaultLanguage}block{else}none{/if};float:left">
						<input type="text" id="name_{$language.id_lang}" name="name_{$language.id_lang}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang)}" style="width:300px" />
					</div>
				{/foreach}
				</div>
				<p class="clear">{l s='Will be displayed in the cart summary as well as on the invoice.'}</p>
			</div>
			<label>{l s='Code'}</label>
			<div class="margin-form">
				<input type="text" id="code" name="code" value="{$currentTab->getFieldValue($currentObject, 'code')}" />
				<img src="../img/admin/news-new.gif" onclick="gencode(8);" style="cursor:pointer" />
				<p>{l s='Optional, the rule will automatically be applied if you leave this field blank.'}</p>
			</div>
			<label>{l s='Partial use'}</label>
			<div class="margin-form">
				&nbsp;&nbsp;
				<input type="radio" name="partial_use" id="partial_use_on" value="1" {if $currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
				<label class="t" for="partial_use_on"> <img src="../img/admin/enabled.gif" alt="{l s='Allowed'}" title="{l s='Allowed'}" style="cursor:pointer" /></label>
				&nbsp;&nbsp;
				<input type="radio" name="partial_use" id="partial_use_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
				<label class="t" for="partial_use_off"> <img src="../img/admin/disabled.gif" alt="{l s='Not allowed'}" title="{l s='Not allowed'}" style="cursor:pointer" /></label>
				<p>
					{l s='Only applicable if the voucher value is greater than the cart total.'}<br />
					{l s='If you do not allow partial use, the voucher value will be lowered to the total order amount, but if you do, a new voucher will be created with the remainder.'}
				</p>
			</div>
			<label>{l s='Priority'}</label>
			<div class="margin-form">
				<input type="text" name="priority" value="{$currentTab->getFieldValue($currentObject, 'priority')|intval}" />
				<p>{l s='Cart rules are applied to the cart by priority. A cart rule with priority of "1" will be processed before a cart rule with a priority of "2".'}</p>
			</div>
			<label>{l s='Status'}</label>
			<div class="margin-form">
				&nbsp;&nbsp;
				<input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
				<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /></label>
				&nbsp;&nbsp;
				<input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
				<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="cursor:pointer" /></label>
			</div>
		</td>
		<td style="width:50%">
			<label>{l s='Description'}</label>
			<div class="margin-form">
				<textarea name="description" style="width:90%;height:200px">{$currentTab->getFieldValue($currentObject, 'description')}</textarea>
				<p>{l s='For you only, never displayed to the customer.'}</p>
			</div>
		</td>
	</tr>
</table>
