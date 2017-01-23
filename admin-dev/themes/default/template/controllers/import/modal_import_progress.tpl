{**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="modal-body">
	<div class="alert alert-warning" id="import_details_stop" style="display:none;">
		Aborting, please wait...
	</div>
	<p id="import_details_progressing">
		Importing your data...
	</p>
	<div class="alert alert-success" id="import_details_finished" style="display:none;">
		Data imported!
		<br/>
		Look at your listings to make sure it's all there as you wished.
	</div>
	<div id="import_messages_div" style="max-height:250px; overflow:auto;">
		<div class="alert alert-danger" id="import_details_error" style="display:none;">
			Errors occurred:<br/><ul></ul>
		</div>
		<div class="alert alert-warning" id="import_details_post_limit" style="display:none;">
			Warning, the current import MAY require a PHP setting update, to allow more
			data to be transferred. If the current import stops before the end, you should increase your PHP "post_max_size" setting to
			<span id="import_details_post_limit_value">16MB</span> at least, and try again.
		</div>
		<div class="alert alert-warning" id="import_details_warning" style="display:none;">
			Some errors were detected. Please check the details:<br/><ul></ul>
		</div>
		<div class="alert alert-info" id="import_details_info" style="display:none;">
			We made the following adjustments:<br/><ul></ul>
		</div>
	</div>

	<div id="import_validate_div" style="margin-top:17px;">
		<div class="pull-right" id="import_validation_details" default-value="Validating data...">
			&nbsp;
		</div>
		<div class="progress active progress-striped" style="display: block; width: 100%">
			<div class="progress-bar progress-bar-info" role="progressbar" style="width: 0%" id="validate_progressbar_done">
				<span><span id="validate_progression_done">0</span>% Validated</span>
			</div>
			<div class="progress-bar progress-bar-info" role="progressbar" id="validate_progressbar_next" style="opacity: 0.5 ;width: 0%">
				<span class="sr-only">Processing next page...</span>
			</div>
		</div>
	</div>

	<div id="import_progress_div" style="display:none;">
		<div class="pull-right" id="import_progression_details" default-value="Importing data...">
			&nbsp;
		</div>
		<div class="progress active progress-striped" style="display: block; width: 100%">
			<div class="progress-bar progress-bar-info" role="progressbar" style="width: 0%" id="import_progressbar_done2">
				<span>Linking accessories...</span>
			</div>
			<div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%" id="import_progressbar_done">
				<span><span id="import_progression_done">0</span>% Imported</span>
			</div>
			<div class="progress-bar progress-bar-success progress-bar-stripes active" role="progressbar" id="import_progressbar_next" style="opacity: 0.5 ;width: 0%">
				<span class="sr-only">Processing next page...</span>
			</div>
		</div>
	</div>

  <div class="modal-footer">
    <div class="input-group pull-right">
      <button type="button" class="btn btn-primary" tabindex="-1" id="import_continue_button" style="display: none;">
        {l s="Ignore warnings and continue?" d="Admin.Advparameters.Notification"}
      </button>
      &nbsp;
      <button type="button" class="btn btn-default" tabindex="-1" id="import_stop_button">
        {l s="Abort import" d="Admin.Advparameters.Feature"}
      </button>
      &nbsp;
      <button type="button" class="btn btn-success" data-dismiss="modal" tabindex="-1" id="import_close_button" style="display: none;">
        {l s="Close" d="Admin.Actions"}
      </button>
    </div>
  </div>
</div>
