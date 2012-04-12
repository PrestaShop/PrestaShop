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
*  @version  Release: $Revision: 9856 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}

{if !$has_shop_selected}
<div class="hint" style="display:block">{l s='Please select the shop you want to configure'}</div>
{else}
	<form action="{$current}&token={$token}" id="{$table}_form" method="post" enctype="multipart/form-data">
		{foreach from=$input_category_list item=category key=name_category}
		<fieldset style="margin: 20px 0;">
			<legend>{$category['title']}</legend>
			{foreach from=$category['fields'] item=input key=input_name}
				<div style="clear: both; padding-top:15px;" id="conf_id_{$input_name}" >
					<label class="conf_title">{$input['title']}</label>
					{if $input['type'] == 'text'}
						<input type="text" name="{$input_name|htmlentities}" {if isset($input['id'])}id="{$input['id']|htmlentities}"{/if} value="{$input['value']|escape:'htmlall':'UTF-8'}" />
					{/if}
					<div class="margin-form">
						{if isset($input['desc'])}<p class="preference_description">{$input['desc']}</p>{/if}
					</div>
				</div>
				<div class="clear"></div>
			{/foreach}
		</fieldset>
		{/foreach}
		<div class="margin-form">
			<input type="submit" class="button" id="{$table}_form_submit_btn" name="update_cfg" value="{l s='Save'}"/>
		</div>
	</form>
{/if}