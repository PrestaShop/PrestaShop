{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<script>
	var labelNext = '{$labels.next|addslashes}';
	var labelPrevious = '{$labels.previous|addslashes}';
	var	labelFinish = '{$labels.finish|addslashes}';
	var	labelDelete = '{l s='Delete' d='Admin.Actions' js=1}';
	var	labelValidate = '{l s='Validate' js=1 d='Admin.Actions'}';
	var validate_url = '{$validate_url|addslashes}';
	var carrierlist_url = '{$carrierlist_url|addslashes}';
	var nbr_steps = {$wizard_steps.steps|count};
	var enableAllSteps = {if $enableAllSteps|intval == 1}true{else}false{/if};
	var need_to_validate = '{l s='Please validate the last range before creating a new one.' js=1 d='Admin.Shipping.Notification'}';
	var delete_range_confirm = '{l s='Are you sure to delete this range ?' js=1 d='Admin.Shipping.Notification'}';
	var currency_sign = '{$currency_sign}';
	var PS_WEIGHT_UNIT = '{$PS_WEIGHT_UNIT}';
	var invalid_range = '{l s='This range is not valid' js=1 d='Admin.Shipping.Notification'}';
	var overlapping_range = '{l s='Ranges are overlapping' js=1 d='Admin.Shipping.Notification'}';
	var range_is_overlapping = '{l s='Ranges are overlapping' js=1 d='Admin.Shipping.Notification'}';
	var select_at_least_one_zone = '{l s='Please select at least one zone' js=1 d='Admin.Shipping.Notification'}';
	var multistore_enable = '{$multistore_enable}';
</script>

<div class="row">
	<div class="col-sm-2">
		{$logo_content}
	</div>
	<div class="col-sm-10">
		<div id="carrier_wizard" class="panel swMain">
			<ul class="steps nbr_steps_{$wizard_steps.steps|count}">
			{foreach from=$wizard_steps.steps key=step_nbr item=step}
				<li>
					<a href="#step-{$step_nbr + 1}">
						<span class="stepNumber">{$step_nbr + 1}</span>
						<span class="stepDesc">
							{$step.title}<br />
							{if isset($step.desc)}<small>{$step.desc}</small>{/if}
						</span>
						<span class="chevron"></span>
					</a>
				</li>
			{/foreach}
			</ul>
			{foreach from=$wizard_contents.contents key=step_nbr item=content}
				<div id="step-{$step_nbr + 1}" class="step_container">
					{$content}
				</div>
			{/foreach}
		</div>
	</div>
</div>
{/block}
