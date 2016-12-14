{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="panel">
	<h3><i class="icon-tag"></i> {l s='Cart rule'}</h3>
	<div class="productTabs">
		<ul class="tab nav nav-tabs">
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_informations" href="javascript:displayCartRuleTab('informations');"><i class="icon-info"></i> {l s='Information'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_conditions" href="javascript:displayCartRuleTab('conditions');"><i class="icon-random"></i> {l s='Conditions'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_actions" href="javascript:displayCartRuleTab('actions');"><i class="icon-wrench"></i> {l s='Actions'}</a>
			</li>
		</ul>
	</div>
	<form action="{$currentIndex|escape}&amp;token={$currentToken|escape}&amp;addcart_rule" id="cart_rule_form" class="form-horizontal" method="post">
		{if $currentObject->id}<input type="hidden" name="id_cart_rule" value="{$currentObject->id|intval}" />{/if}
		<input type="hidden" id="currentFormTab" name="currentFormTab" value="informations" />
		<div id="cart_rule_informations" class="panel cart_rule_tab">
			{include file='controllers/cart_rules/informations.tpl'}
		</div>
		<div id="cart_rule_conditions" class="panel cart_rule_tab">
			{include file='controllers/cart_rules/conditions.tpl'}
		</div>
		<div id="cart_rule_actions" class="panel cart_rule_tab">
			{include file='controllers/cart_rules/actions.tpl'}
		</div>
		<button type="submit" class="btn btn-default pull-right" name="submitAddcart_rule" id="{$table|escape}_form_submit_btn">{l s='Save' d='Admin.Actions'}
		</button>
		<!--<input type="submit" value="{l s='Save and stay' d='Admin.Actions'}" class="button" name="submitAddcart_ruleAndStay" id="" />-->
	</form>

	<script type="text/javascript">
		var product_rule_groups_counter = {if isset($product_rule_groups_counter)}{$product_rule_groups_counter|intval}{else}0{/if};
		var product_rule_counters = new Array();
		var currentToken = '{$currentToken|escape:'quotes'}';
		var currentFormTab = '{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'quotes'}{else}informations{/if}';
		var currentText = '{l s='Now' js=1}';
		var closeText = '{l s='Done' js=1}';
		var timeOnlyTitle = '{l s='Choose Time' js=1}';
		var timeText = '{l s='Time' js=1}';
		var hourText = '{l s='Hour' js=1}';
		var minuteText = '{l s='Minute' js=1}';
		
		var languages = new Array();
		{foreach from=$languages item=language key=k}
			languages[{$k}] = {
				id_lang: {$language.id_lang},
				iso_code: '{$language.iso_code|escape:'quotes'}',
				name: '{$language.name|escape:'quotes'}'
			};
		{/foreach}
		displayFlags(languages, {$id_lang_default});
	</script>
	<script type="text/javascript" src="themes/default/template/controllers/cart_rules/form.js"></script>
	{include file="footer_toolbar.tpl"}
</div>
