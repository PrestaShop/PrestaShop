{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<div class="panel">
	<h3><i class="icon-tag"></i> {l s='Cart rule' d='Admin.Catalog.Feature'}</h3>
	<div class="productTabs">
		<ul class="tab nav nav-tabs">
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_informations" href="javascript:displayCartRuleTab('informations');"><i class="icon-info"></i> {l s='Information' d='Admin.Catalog.Feature'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_conditions" href="javascript:displayCartRuleTab('conditions');"><i class="icon-random"></i> {l s='Conditions' d='Admin.Catalog.Feature'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" id="cart_rule_link_actions" href="javascript:displayCartRuleTab('actions');"><i class="icon-wrench"></i> {l s='Actions' d='Admin.Global'}</a>
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
		var currentFormTab = '{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'html'}{else}informations{/if}';
		var currentText = '{l s='Now' js=1 d='Admin.Catalog.Feature'}';
		var closeText = '{l s='Done' js=1 d='Admin.Catalog.Feature'}';
		var timeOnlyTitle = '{l s='Choose Time' js=1 d='Admin.Catalog.Feature'}';
		var timeText = '{l s='Time' js=1 d='Admin.Catalog.Feature'}';
		var hourText = '{l s='Hour' js=1 d='Admin.Global'}';
		var minuteText = '{l s='Minute' js=1 d='Admin.Catalog.Feature'}';

    {if isset($refresh_cart) }
      if (typeof window.parent.order_create !== "undefined") {
        window.parent.order_create.refreshCart();
      }
      window.parent.$.fancybox.close();
    {/if}

  </script>
	<script type="text/javascript" src="themes/default/template/controllers/cart_rules/form.js"></script>
	{include file="footer_toolbar.tpl"}
</div>
