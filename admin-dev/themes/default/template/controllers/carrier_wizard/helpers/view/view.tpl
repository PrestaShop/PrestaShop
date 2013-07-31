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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2013 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<script>
	var labelNext = '{$labels.next|addslashes}';
	var labelPrevious = '{$labels.previous|addslashes}';
	var	labelFinish = '{$labels.finish|addslashes}';
	var	labelDelete = '{l s='Delete' js=1}';
	var	labelValidate = '{l s='Validate' js=1}';
	var validate_url = '{$validate_url|addslashes}';
	var carrierlist_url = '{$carrierlist_url|addslashes}';
	var nbr_steps = {$wizard_steps.steps|count};
	var enableAllSteps = {if $enableAllSteps|intval == 1}true{else}false{/if};
	var need_to_validate = '{l s='Please validate the last range before create a new one.' js=1}';
	var delete_range_confirm = '{l s='Are you sure to delete this range ?' js=1}';
</script>
<div id="carrier_wizard" class="swMain">
	<ul class="nbr_steps_{$wizard_steps.steps|count}">
	{foreach from=$wizard_steps.steps key=step_nbr item=step}
		<li>
			<a href="#step-{$step_nbr + 1}">
				<label class="stepNumber">{$step_nbr + 1}</label>
				<span class="stepDesc">
					{$step.title}<br />
					{if isset($step.desc)}<small>{$step.desc}</small>{/if}
				</span>
			</a>
		</li>
	{/foreach}
	</ul>
	{foreach from=$wizard_contents.contents key=step_nbr item=content}
		<div id="step-{$step_nbr + 1}" style="padding-bottom:10px"> 	
			{$content}
		</div>
	{/foreach}
</div>
{/block}
