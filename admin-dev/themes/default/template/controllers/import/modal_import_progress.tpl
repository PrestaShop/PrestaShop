{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="modal-body">
    <p id="import_details_progressing">
        Please wait, Preston is trying to import your Excel shit...
        <br/>
        // TODO @Julie and @Alex, if want a better wording...
        <br/>
        // TODO: Put a happy dancing Preston here...
    </p>
    <div class="alert alert-success" id="import_details_finished" style="display:none;">
        Thank you for your patience! You can now close this dialog box with
        the little cross at the top right corner, and look after you data
        somewhere in the database...
        <br/>
        Good luck!
    </div>
    <div class="alert alert-warning" id="import_details_error" style="display:none;">
        &nbsp;
    </div>
    
    <div id="import_progress_div">
        <div class="pull-right" id="import_progression_details">
            &nbsp;
        </div>
        <div class="progress" style="display: block; width: 100%">
            <div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%" id="import_progressbar_done">
                <span><span id="import_progression_done">0</span>% Complete</span>
            </div>
            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" id="import_progressbar_next" style="opacity: 0.5 ;width: 0%">
                <span class="sr-only">Processing next page...</span>
            </div>
        </div>
    </div>
    
    <div class="input-group">
		<button type="button" class="btn btn-default" tabindex="-1" disabled="disabled" id="import_stop_button">
			<i class="icon-flag"></i>
			I give up! Please stop!
		</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" tabindex="-1" id="import_close_button" style="display: none;">
            Close me, I'm useless now!
        </button>
	</div>
</div>