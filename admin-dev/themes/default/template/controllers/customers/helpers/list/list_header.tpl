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

{extends file="helpers/list/list_header.tpl"}
{block name='override_header'}
{if $submit_form_ajax}
	<script type="text/javascript">
		$('#customer', window.parent.document).val('{$new_customer->email|escape:htmlall}');
		parent.setupCustomer({$new_customer->id|intval});
		parent.$.fancybox.close();
	</script>
{/if}
{/block}
{block name=leadin}
	{if isset($delete_customer) && $delete_customer}
		<form action="{$REQUEST_URI}" method="post">
			<div class="warn">
				<h2>{l s='How do you want to delete these customer(s)?'}</h2>
				<p>{l s='There are two ways of deleting a customer. Please choose your preferred method.'}</p>
				<ul class="listForm">
				<li>
					<input type="radio" name="deleteMode" value="real" id="deleteMode_real" />
					<label for="deleteMode_real" style="float:none;">{l s='I want to delete my customer(s) (All data will be removed from the database, and customers with the same e-mail address will be able to re-register.'}</label>
				</li>
				<li>
					<input type="radio" name="deleteMode" value="deleted" id="deleteMode_deleted" />
					<label for="deleteMode_deleted" style="float:none">{l s='I don\'t want my customer(s) to register again. Therefore, each customer(s) will be removed from this list but all corresponding data will be kept in the database.'}</label>
				</li>
				</ul>
				{foreach $POST as $key => $value}
					{if is_array($value)}
						{foreach $value as $val}
							<input type="hidden" name="{$key}[]" value="{$val}" />
						{/foreach}
					{else}
						<input type="hidden" name="{$key}" value="{$value}" />
					{/if}
				{/foreach}
				<br /><input type="submit" class="button" value="{l s='Delete'}" />
			</div>
		</form>
		<div class="clear">&nbsp;</div>
		<script>
			$(document).ready(function() {
				$('table[name=\'list_table\']').hide();
			});
		</script>
	{/if}
{/block}
