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
{if isset($groups)}
<hr width="99%" align="center" size="2" class="margin_less"/>

<div id="attributes">
{foreach from=$groups key=id_attribute_group item=group}
	{if $group.attributes|@count}
	<div class="attributes_group">
		{capture assign='groupName'}group_{$id_attribute_group|intval}{/capture}
		<label class="attribute_label" for="{$groupName}">{$group.name|escape:'htmlall':'UTF-8'} :</label>
		{if ($group.group_type == 'select' || $group.group_type == 'color')}
			<select name="{$groupName}" id="{$groupName}" class="attribute_select{if ($group.group_type == 'color')} select_color{/if}">
				{foreach from=$group.attributes key=id_attribute item=group_attribute}
					<option value="{$id_attribute|intval}" title="{$group_attribute|escape:'htmlall':'UTF-8'}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if}>{$group_attribute|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		{elseif ($group.group_type == 'radio')}
			<fieldset data-role="controlgroup">
			{foreach from=$group.attributes key=id_attribute item=group_attribute}
				<input type="radio" class="attribute_radio" name="{$groupName}" id="{$groupName}_{$id_attribute}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if}>
				<label for="{$groupName}_{$id_attribute}">{$group_attribute|escape:'htmlall':'UTF-8'}</label>
			{/foreach}
			</fieldset>
		{/if}
	</div>
	{/if}
{/foreach}
</div><!-- #attributes -->
{/if}
