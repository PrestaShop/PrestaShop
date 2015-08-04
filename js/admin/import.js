/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function(){

	$('#saveImportMatchs').unbind('click').click(function(){

	var newImportMatchs = $('#newImportMatchs').attr('value');
	if (newImportMatchs == '')
		jAlert(errorEmpty);
	else
	{
		var matchFields = '';
		$('.type_value').each( function () {
			matchFields += '&'+$(this).attr('id')+'='+$(this).attr('value');
		});
		$.ajax({
	       type: 'POST',
	       url: 'index.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=1&action=saveImportMatchs&tab=AdminImport&token=' + token + '&skip=' + $('input[name=skip]').attr('value') + '&newImportMatchs=' + newImportMatchs + matchFields,
	       success: function(jsonData)
	       {
				$('#valueImportMatchs').append('<option id="'+jsonData.id+'" value="'+matchFields+'" selected="selected">'+newImportMatchs+'</option>');
				$('#selectDivImportMatchs').fadeIn('slow');
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown)
	       {
	       		jAlert('TECHNICAL ERROR Details: ' + html_escape(XMLHttpRequest.responseText));
	       }
	   });

	}
	});

	$('#loadImportMatchs').unbind('click').click(function(){

		var idToLoad = $('select#valueImportMatchs option:selected').attr('id');
		$.ajax({
		       type: 'POST',
		       url: 'index.php',
		       async: false,
		       cache: false,
		       dataType : "json",
		       data: 'ajax=1&action=loadImportMatchs&tab=AdminImport&token=' + token + '&idImportMatchs=' + idToLoad,
		       success: function(jsonData)
		       {
					var matchs = jsonData.matchs.split('|')
					$('input[name=skip]').val(jsonData.skip);
					for (i=0;i<matchs.length;i++)
						$('#type_value\\['+i+'\\]').val(matchs[i]).attr('selected',true);
		       },
		      error: function(XMLHttpRequest, textStatus, errorThrown)
		       {
		       		jAlert('TECHNICAL ERROR Details: ' + html_escape(XMLHttpRequest.responseText));

		       }
		   });
	});

	$('#deleteImportMatchs').unbind('click').click(function(){

		var idToDelete = $('select#valueImportMatchs option:selected').attr('id');
		$.ajax({
		       type: 'POST',
		       url: 'index.php',
		       async: false,
		       cache: false,
		       dataType : "json",
		       data: 'ajax=1&action=deleteImportMatchs&tab=AdminImport&token=' + token + '&idImportMatchs=' + idToDelete ,
		       success: function(jsonData)
		       {
					$('select#valueImportMatchs option[id=\''+idToDelete+'\']').remove();
					if ($('select#valueImportMatchs option').length == 0)
						$('#selectDivImportMatchs').fadeOut();
		       },
		      error: function(XMLHttpRequest, textStatus, errorThrown)
		       {
		       		jAlert('TECHNICAL ERROR Details: ' + html_escape(XMLHttpRequest.responseText));
		       }
		   });

	});
});

function validateImportation(mandatory)
{
    var type_value = [];
	var seted_value = [];
	var elem;
	var col = 'unknow';

	toggle(getE('error_duplicate_type'), false);
	toggle(getE('required_column'), false);
    for (i = 0; elem = getE('type_value['+i+']'); i++)
    {
		if (seted_value[elem.options[elem.selectedIndex].value])
		{
			scroll(0,0);
			toggle(getE('error_duplicate_type'), true);
			return false;
		}
		else if (elem.options[elem.selectedIndex].value != 'no')
			seted_value[elem.options[elem.selectedIndex].value] = true;
	}
	for (needed in mandatory)
		if (!seted_value[mandatory[needed]])
		{
			scroll(0,0);
			toggle(getE('required_column'), true);
			getE('missing_column').innerHTML = mandatory[needed];
			elem = getE('type_value[0]');
			for (i = 0; i < elem.length; ++i)
			{
				if (elem.options[i].value == mandatory[needed])
				{
					getE('missing_column').innerHTML = elem.options[i].innerHTML;
					break ;
				}
			}
			return false
		}
	
	importNow(0, 5, -1, {}); // starts with 5 elements to import, but the limit will be adapted for next calls automatically.
	return false; // We return false to avoid form to be posted on the old Controller::postProcess() action
}

function importNow(offset, limit, total, crossStepsVariables) {
	if (offset == 0) updateProgressionInit();

	var data = $('form#import_form').serializeArray();
	data.push({'name': 'crossStepsVars', 'value': JSON.stringify(crossStepsVariables)});
	
    var startingTime = new Date().getTime();
    $.ajax({
       type: 'POST',
       url: 'index.php?ajax=1&action=import&tab=AdminImport&offset='+offset+'&limit='+limit+'&token='+token,
       cache: false,
       dataType: "json",
       data: data,
       success: function(jsonData)
       {
    	   if (jsonData.totalCount) {
    		   total = jsonData.totalCount;
    	   }
    		   
    	   if (!jsonData.isFinished == true) {
	    	   // compute time taken by previous call to adapt amount of elements by call.
	    	   var previousDelay = new Date().getTime() - startingTime;
	    	   var targetDelay = 5000; // try to keep around 5 seconds by call
	    	   // acceleration will be limited to 4 to avoid newLimit to increase too fast (NEVER reach 30 seconds by call!).
	    	   var acceleration = Math.min(4, (targetDelay / previousDelay));
	    	   // keep between 5 to 75 elements to import in one call
	    	   var newLimit = Math.min(75, Math.max(5, Math.floor(limit * acceleration)));
	    	   var newOffset = offset + limit;
	    	   // update progression
	    	   updateProgression(jsonData.doneCount, total, jsonData.doneCount+newLimit);
	    	   // import next group of elements
	    	   importNow(newOffset, newLimit, total, jsonData.crossStepsVariables);
	    	   
	    	   // checks if we could go over post_max_size setting. Warns when reach 90% of the actual setting
	    	   if (jsonData.nextPostSize >= jsonData.postSizeLimit * 0.9) {
	    		   var progressionDone = jsonData.doneCount * 100 / total;
	    		   var increase = Math.max(7, parseInt(jsonData.postSizeLimit/(progressionDone*1024*1024))) + 1; // min 8MB
	    		   $('#import_details_post_limit_value').html(increase+" MB");
	    		   $('#import_details_post_limit').show();
	    	   }
	    	   
	       } else {
	    	   // update progression (will close popin)
	    	   updateProgression(total, total, total);
	       }
       },
       error: function(XMLHttpRequest, textStatus, errorThrown)
       {
    	   updateProgressionError(textStatus);
       }
	});
}

function updateProgressionInit() {
	$('#importProgress').modal({backdrop: 'static', keyboard: false, closable: false});
	$('#importProgress').modal('show');
	
	$('#import_details_progressing').show();
	$('#import_details_finished').hide();
	$('#import_details_error').hide();
	
	$('#import_progression_details').html('&nbsp;');
	$('#import_progressbar_done').width('0%');
	$('#import_progression_done').html('0');
	$('#import_progressbar_next').width('0%');	
	
	$('#import_stop_button').show();
	$('#import_close_button').hide();
}

function updateProgression(currentPosition, total, nextPosition) {
	if (currentPosition > total) currentPosition = total;
	if (nextPosition > total) nextPosition = total;
	
	var progressionDone = currentPosition * 100 / total;
	var progressionNext = nextPosition * 100 / total;
	
	if (total > 0) {
		$('#import_progression_details').html(currentPosition + '/' + total);
		$('#import_progressbar_done').width(progressionDone+'%');
		$('#import_progression_done').html(parseInt(progressionDone));
		$('#import_progressbar_next').width((progressionNext-progressionDone)+'%');	
	}
	
	if (currentPosition == total && total == nextPosition) {
		$('#import_details_post_limit').hide();
		$('#import_details_progressing').hide();
		$('#import_details_finished').show();
		$('#importProgress').modal({keyboard: true, closable: true});
		$('#import_stop_button').hide();
		$('#import_close_button').show();
	}
}

function updateProgressionError(message) {
	$('#import_details_progressing').hide();
	$('#import_details_finished').hide();
	$('#import_details_error').html(message);
	$('#import_details_error').show();
	
	$('#import_progressbar_next').addClass('progress-bar-danger');
	$('#import_progressbar_next').removeClass('progress-bar-success');
	
	$('#import_stop_button').hide();
	$('#import_close_button').show();
}

function html_escape(str) {
    return String(str)
	    .replace(/&/g, '&amp;')
	    .replace(/"/g, '&quot;')
	    .replace(/'/g, '&#39;')
	    .replace(/</g, '&lt;')
	    .replace(/>/g, '&gt;');
}
