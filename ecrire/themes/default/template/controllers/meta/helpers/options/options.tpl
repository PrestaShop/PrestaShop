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
*  @version  Release: $Revision: 13686 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/options/options.tpl"}
{block name="input"}
	{if $field['type'] == 'rewriting_settings'}
			<label class="t" for="{$key}_on"><img src="../img/admin/enabled.gif" alt="{l s='Yes'}" title="{l s='Yes'}" /></label>
			<input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}/>
			<label class="t" for="{$key}_on"> {l s='Yes'}</label>
			<label class="t" for="{$key}_off"><img src="../img/admin/disabled.gif" alt="{l s='No'}" title="{l s='No'}" style="margin-left: 10px;" /></label>
			<input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}/>
			<label class="t" for="{$key}_off"> {l s='No'}</label>
		{if !$field['mod_rewrite']}
			<span class="warning_mod_rewrite">{l s='URL rewriting (mod_rewrite) is not active on your server or it is not possible to check your server configuration. If you want to use Friendly URLs you must activate this mod.'}</span>
			<div class="clear"></div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

