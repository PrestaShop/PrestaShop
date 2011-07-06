var toggle_status_order_list = false;
var toggle_history_order_list = false;

function toggleOrderListSelection()
{
	toggle_status_order_list = !toggle_status_order_list;

	$('input[name="order_id_list[]"]').attr('checked', toggle_status_order_list);
}

function toggleHistoryListSelection()
{
	toggle_history_order_list = !toggle_history_order_list;

	$('input[name="history_id_list[]"]').attr('checked', toggle_history_order_list);
}

function getTickets(detailedExpeditionList)
{
	$.ajax(
	{
		type : 'POST',
		url : _PS_MR_MODULE_DIR_ + 'ajax.php',
		data :	{'detailedExpeditionList':detailedExpeditionList, 
				'method' : 'MRGetTickets'},
		dataType: 'json',
		success: function(json) 
		{
			if (json && json.success)
				for (id_order in json.success)
					if (json.success[id_order])
					{
						$('#URLA4_' + id_order).html('<a href="' + json.success[id_order].URLPDF_A4 + '">\
								<img width="20" src="' + _PS_MR_MODULE_DIR_ + 'images/pdf_icon.jpg" alt="download pdf"" /></a>');
						$('#URLA5_' + id_order).html('<a href="' + json.success[id_order].URLPDF_A5 + '">\
								<img width="20" src="' + _PS_MR_MODULE_DIR_ + 'images/pdf_icon.jpg" alt="download pdf"" /></a>');
						$('#expeditionNumber_' + id_order).html(json.success[id_order].expeditionNumber);
						$('#detailHistory_' + id_order).children('td').children('input').attr('value', json.success[id_order].id_mr_history);
						$('#detailHistory_' + id_order).children('td').children('input').attr('id', 'PS_MRHistoryId_' + json.success[id_order].id_mr_history);
					}
			displayBackGenerateSubmitButton();
			displayBackHistoriesSubmitButton();
		},
		error: function(xhr, ajaxOptions, thrownError)
		{
            //console.log(thrownError);
            displayBackGenerateSubmitButton();
		}
	});
}

function checkErrorGenetateTickets(json)
{
	i = 0;
	$('.PS_MRErrorList').fadeOut('fast', function()
	{
		if ((++i >= $('.PS_MRErrorList').length) && json && json.error)
			for (id_order in json.error)
				if (json.error[id_order] && json.error[id_order].length)
				{
					$('#errorCreatingTicket_' + id_order).children('td').children('span').html('');
					$('#errorCreatingTicket_' + id_order).fadeOut('slow');
					$('#errorCreatingTicket_' + id_order).fadeIn('slow');
					for (numError in json.error[id_order])
						$('#errorCreatingTicket_' + id_order).children('td').children('span').append(json.error[id_order][numError] + '<br >');
				}
	});
	checkOtherErrors(json);
}

function checkOtherErrors(json)
{
	$('#otherErrors').fadeOut('fast', function()
	{
		
		if (json && json.other && json.other.error)
		for (numError in json.other.error)
			if (json.other.error[numError])
			{
				$('#otherErrors').fadeIn('slow');
				$('#otherErrors').children('span').html('');
				$('#otherErrors').children('span').append(json.other.error[numError]);
			}
	});
}

function checkSucceedGenerateTickets(json)
{
	detailedExpeditionList = new Array();
	
	i = 0;
	$('.PS_MRSuccessList').fadeOut('fast', function()
	{
		if ((++i >= $('.PS_MRSuccessList').length) && json && json.success)
		{
			for (id_order in json.success)
				if (json.success[id_order] && json.success[id_order].expeditionNumber)
				{
					$('#successCreatingTicket_' + id_order).children('td').children('span').html('');
					$('#PS_MRLineOrderInformation-' + id_order).remove();
					$('#successCreatingTicket_' + id_order).fadeIn('slow');
					detailedExpeditionList.push({'id_order':id_order, 'expeditionNumber':json.success[id_order].expeditionNumber});
					
					if (!$('#detailHistory_' + id_order).length)
						$('#PS_MRHistoriqueTableList').append('\
							<tr id="detailHistory_' + id_order + '">\
								<td><input type="checkbox" class="history_id_list" name="history_id_list[]" value="' + id_order + '" /></td>\
								<td>' + id_order + '</td>\
								<td id="expeditionNumber_' + id_order + '"><img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" /></td>\
								<td id="URLA4_' + id_order + '"><img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" /></td>\
								<td id="URLA5_' + id_order + '"><img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" /></td>\
							</tr>');
					else
					{	
						$('#detailHistory_' + id_order).children('#URLA4_' + id_order).html('<img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" />');
						$('#detailHistory_' + id_order).children('#URLA5_' + id_order).html('<img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" />');
						$('#detailHistory_' + id_order).children('#expeditionNumber_' + id_order).html('<img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" />');
					}
				}
		}
	});
	return detailedExpeditionList;
}

function displayBackGenerateSubmitButton()
{
	$('#PS_MRSubmitGenerateLoader').css('display', 'none');
	if ($('.order_id_list').length)
		$('#PS_MRSubmitButtonGenerateTicket').fadeIn('slow');	
}

function displayBackHistoriesSubmitButton()
{
	$('#PS_MRSubmitDeleteHistoriesLoader').css('display', 'none');
	if ($('.history_id_list').length)
		$('#PS_MRSubmitButtonDeleteHistories').fadeIn('slow');	
}

function generateTicketsAjax()
{
	var order_id_list = new Array();
	var weight_list = new Array();
	
	$('#PS_MRSubmitButtonGenerateTicket').css('display', 'none');
	$('#PS_MRSubmitGenerateLoader').fadeIn('slow');
	
	numSelected = $('input[name="order_id_list[]"]:checked').length;
	$('input[name="order_id_list[]"]:checked').each(function()
	{
		order_id_list.push($(this).val());
		weight_list.push(($('#weight_' + $(this).val()).val()) + '-' + $(this).val());
	});
	
	$.ajax(
	{
		type : 'POST',
		url : _PS_MR_MODULE_DIR_ + 'ajax.php',
		data :	{'order_id_list' : order_id_list, 
				'numSelected' : numSelected,
				'weight_list' : weight_list,
				'method' : 'MRCreateTickets'},
		dataType: 'json',
		success: function(json) 
		{
			detailedExpeditionList = new Array();
			
			checkErrorGenetateTickets(json);
			detailedExpeditionList = checkSucceedGenerateTickets(json);
			
			if (detailedExpeditionList.length)
				getTickets(detailedExpeditionList);
			else
				displayBackGenerateSubmitButton();
		},
		error: function(xhr, ajaxOptions, thrownError)
		{
			display_generate_button = true;
            displayBackGenerateSubmitButton();
		}
	});
	delete(order_id_list);
	delete(weight_list);
}

function displayDeletedHistoryInformation()
{
  :x


	$('input[name="history_id_list[]"]:checked').each(function()
	{
		$(this).parent().parent().css('background-color', '#FFE2E3');
	});
	displayBackHistoriesSubmitButton();
}

function checkDeletedHistoriesId(json)
{
	if (json && json.success)
	{
		// Allow to wait the end of the loop to manage unremoved item
		i = 0;
		for (numberHistoryId in json.success.deletedListId)
		{
			$('#PS_MRHistoryId_' + json.success.deletedListId[numberHistoryId]).parent().parent().fadeOut('fast', function()
			{
				$(this).remove();
				// Fadeout is asynchome verify everytime the number element
				if (++i == json.success.deletedListId.length)
					displayDeletedHistoryInformation(json.success.deletedListId.length);
			});
		}
		// Use if none element exist in the list
		if (i == json.success.deletedListId.length)
			displayDeletedHistoryInformation();
	}
	else
		displayBackHistoriesSubmitButton();
}

function deleteSelectedHistories()
{
	var history_id_list = new Array();
	
	$('#PS_MRSubmitButtonDeleteHistories').css('display', 'none');
	$('#PS_MRSubmitDeleteHistoriesLoader').fadeIn('slow');
	
	numSelected = $('input[name="order_id_list[]"]:checked').length;
	$('input[name="history_id_list[]"]:checked').each(function()
	{
		history_id_list.push($(this).val());
	});
	
	$.ajax(
	{
		type : 'POST',
		url : _PS_MR_MODULE_DIR_ + 'ajax.php',
		data :	{'history_id_list' : history_id_list, 
				'numSelected' : numSelected,
				'method' : 'DeleteHistory'},
		dataType: 'json',
		success: function(json)
		{
			checkOtherErrors(json);
			checkDeletedHistoriesId(json);
		},
		error: function(xhr, ajaxOptions, thrownError)
		{
			display_generate_button = true;
            displayBackHistoriesSubmitButton();
		}
	});
}

$(document).ready(function()
{
	$('#toggleStatusOrderList').click(function()
	{
		toggleOrderListSelection();
	});
	$('#toggleStatusHistoryList').click(function()
	{
		toggleHistoryListSelection();
	});
	$('#generate').click(function()
	{
		generateTicketsAjax();
	});
	$('#PS_MRSubmitButtonDeleteHistories').click(function()
	{
		deleteSelectedHistories();
	});
});
