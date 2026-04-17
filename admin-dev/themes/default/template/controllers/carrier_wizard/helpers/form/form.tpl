{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{extends file="helpers/form/form.tpl"}
{block name="script"}
	var string_price = '{l s='Will be applied when the price is' js=1 d='Admin.Shipping.Feature'}';
	var string_weight = '{l s='Will be applied when the weight is' js=1 d='Admin.Shipping.Feature'}';
{/block}

{block name="field"}
	{if $input.name == 'zones'}
		<div class="ranges_not_follow warn" style="display:none">
			<label>{l s='Ranges are not correctly ordered:' d='Admin.Shipping.Notification'}</label>
			<a href="#" onclick="checkRangeContinuity(true); return false;" class="btn btn-default">{l s='Reordering' d='Admin.Shipping.Notification'}</a>
		</div>
		{include file='controllers/carrier_wizard/helpers/form/form_ranges.tpl'}

		<div class="new_range">
			<a href="#" onclick="add_new_range();return false;" class="btn btn-default" id="add_new_range">{l s='Add new range' d='Admin.Shipping.Feature'}</a>
		</div>
	{/if}
	{if $input.name == 'logo'}
		<div class="col-lg-8">
			<input id="carrier_logo_input" class="hide" type="file" onchange="uploadCarrierLogo();" name="carrier_logo_input" />
			<input type="hidden" id="logo" name="logo" value="" />
			<div class="dummyfile input-group">
				<span class="input-group-addon"><i class="icon-file"></i></span>
				<input id="attachement_filename" type="text" name="filename" readonly="" />
				<span class="input-group-btn">
					<button id="attachement_fileselectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
						<i class="icon-folder-open"></i> {l s='Choose a file' d='Admin.Actions'}
					</button>
				</span>
			</div>
			<p class="help-block">
					{l s='Format:' d='Admin.Shipping.Help'} JPG, GIF, PNG, WEBP. {l s='Filesize:' d='Admin.Shipping.Help'} {$max_image_size|string_format:"%.2f"} {l s='MB max.' d='Admin.Shipping.Help'}
					{l s='Current size:' d='Admin.Shipping.Help'} <span id="carrier_logo_size">{l s='undefined' d='Admin.Shipping.Help'}</span>.
			</p>
		</div>
	{/if}
	{$smarty.block.parent}
{/block}
