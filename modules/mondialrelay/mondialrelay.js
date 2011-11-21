var toggle_status_order_list = false;
var toggle_history_order_list = false;
var relay_point_max = 10;
var last_gmap_info_clicked = '';

// /!\ All the following list data could be store into the same variable
// But for a better reading of the code, there are separated

// List of the supplier id which the user trying to fetch the relay
var fetchingRelayPoint = new Array();

// List of the relay point object
var relayPointDataContainers = new Array();

// List of the google map object
var GmapList = new Object();

// List the marker liable to the relay pint
var markerList = new Object();

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
		data :	{'detailedExpeditionList':detailedExpeditionList, 'method':'MRGetTickets', 'mrtoken':mrtoken},
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
					{
						$('#PS_MRHistoriqueTableList').append('\
							<tr id="detailHistory_' + id_order + '">\
								<td><input type="checkbox" class="history_id_list" name="history_id_list[]" value="' + id_order + '" /></td>\
								<td>' + id_order + '</td>\
								<td id="expeditionNumber_' + id_order + '"><img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" /></td>\
								<td id="URLA4_' + id_order + '"><img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" /></td>\
								<td id="URLA5_' + id_order + '"><img src="' + _PS_MR_MODULE_DIR_ + 'images/getTickets.gif" /></td>\
							</tr>');
					} 
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
				'method' : 'MRCreateTickets',
				'mrtoken' : mrtoken},
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
	$('input[name="history_id_list[]"]:checked').each(function()
	{
		$(this).parent().parent().css('background-color', '#FFE2E3');
	});
	displayBackHistoriesSubmitButton();
}

/*
** Manage the removed histories id
*/
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

/*
** Delete the histories selected by the merchant 
*/
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
				'method' : 'DeleteHistory',
				'mrtoken' : mrtoken},
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

/*
** Display a fancy box displaying details about the 
** backup of the database
*/
function PS_MRGetUninstallDetail()
{
	$.ajax(
	{
		type: 'POST',
		url: _PS_MR_MODULE_DIR_ + 'ajax.php',
		data: {'method' : 'uninstallDetail',
						'action' : 'showFancy',
						'href' : targetButton,
						'mrtoken' : mrtoken},
		dataType: 'json',
		success: function(json) 
		{
			((json.html.length) ?
				$.fancybox(json.html, 
				{
					'autoDimensions'	: false,
					'width'						: 450,
					'height'					: 'auto',
					'transitionIn'		: 'none',
					'transitionOut'		: 'none',
					'onComplete'			: function()
						{
							PS_MRHandleUninstallButton();
							
							// Rewrite some css properties of Fancybox 
							$('#fancybox-wrap').css('width', '');
							$('#fancybox-content').css('background-color', '');
							$('#fancybox-content').css('border', '');
						}
				})
				: '');
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			// Put debug to see error detail
		}
	});
	return false;
}

/*
** Handle the button when a user clicked on the uninstall button
*/
function PS_MRHandleUninstallButton()
{
	$('#PS_MR_BackupAction').click(function()
	{
		$.fancybox.close();
		PS_MRBackupDatabase();
	});
	
	$('#PS_MR_UninstallAction').click(function()
	{
		window.location.href = targetButton;
		$.fancybox.close();
		return true;
	});
	
	$('#PS_MR_StopUninstall').click(function()
	{
		$.fancybox.close();
		return false;
	});
}

/*
** ajax call to keep the database of the module safe
*/
function PS_MRBackupDatabase()
{	
	$.ajax(
	{
		type: 'POST',
		url: _PS_MR_MODULE_DIR_ + 'ajax.php',
		data: {'method' : 'uninstallDetail',
					'action' : 'backupAndUninstall',
					'mrtoken' : mrtoken},
		dataType: 'json',
		success: function(json) 
		{
			targetButton += '&keepDatabase=true';
			window.location.href = targetButton;		
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			// Put debug to see error detail
		}
	});
}

/*
** Add / update a entry to the selected carrier to the mr_selected table
** with the selected relay point information
*/
function PS_MRAddSelectedRelayPointInDB(relayPointNumber, id_carrier)
{
	PS_MRSelectedRelayPoint['relayPointNum'] = relayPointNumber;
	
	// Ajax call to add the selection in the database (compatibility for 1.3)
	// But keep this way to add a selection better that the hook
	MRjQuery.ajax({
		type: 'POST',
    url: _PS_MR_MODULE_DIR_ + 'ajax.php',
    data: {'method' : 'addSelectedCarrierToDB',
    	'relayPointInfo' : relayPointDataContainers[relayPointNumber],
    	'id_carrier' : id_carrier,
    	'id_mr_method' : PS_MRCarrierMethodList[id_carrier],
    	'mrtoken' : mrtoken},
    success: function(json)
    {
			if (PS_MROPC)
				updateCarrierSelectionAndGift();
    },
    error: function(xhr, ajaxOptions, thrownError)
    {
    }
	});
}

/*
** Add / update a entry to the selected carrier to the mr_selected table
** Without relay point information
*/
function PS_MRAddSelectedCarrierInDB(id_carrier)
{
	PS_MRHideLastRelayPointList();

	// Make the request
	$.ajax({
		type: 'POST',
    url: _PS_MR_MODULE_DIR_ + 'ajax.php',
    data: {'method' : 'addSelectedCarrierToDB',
    	'id_carrier' : id_carrier,
    	'id_mr_method' : PS_MRCarrierMethodList[id_carrier],
    	'mrtoken' : mrtoken},
    success: function(json)
    {
    },
    error: function(xhr, ajaxOptions, thrownError)
    {
    }
	});
}

function PS_MRCarrierSelectedProcess(carrierSelected, id_carrier, MRLivraisonType)
{
	// Reset for any carrier changement
	if (MRLivraisonType != 'LD1' &&	MRLivraisonType != 'LDS')
	{
		// Seek Relay point if it doesn't a home delivery mode
		PS_MRGetRelayPoint(carrierSelected);
		
		// Will have relay points
		PS_MRSelectedRelayPoint['relayPointNum'] = 0;
	}
	else
	{
		// Simply add the selected carrier to the db, relay information will be empty
		PS_MRAddSelectedCarrierInDB(id_carrier);
		
		// Won't have any relay points
		PS_MRSelectedRelayPoint['relayPointNum'] = -1;
}
}

/*
** Hide the last displayed relay point list
*/
function PS_MRHideLastRelayPointList()
{
	$('.PS_MRSelectedCarrier').fadeOut('fast');
}

/*
** Check if the user select a carrier and a relay point if exist
*/
function PS_MRCheckSelectedRelayPoint()
{
	var input;
	
	// Check if the input is linked to the module and look into
	// a temporary variable if a relay point has been selected
	if ((input = $('input[name=id_carrier]:checked')).length &&
		PS_MRCarrierMethodList[input.val()] != undefined &&
		PS_MRSelectedRelayPoint['relayPointNum'] == 0)
	{
		//$('#PS_MRSelectCarrierError').fadeIn('fast');
		alert(PS_MRTranslationList['errorSelection']);
		return false;
	}		
	return true;
}

/*
** Link the generated relay point to an handle click
** Allow to add the selected relay point in the database
*/
function PS_MRHandleSelectedRelayPoint()
{	
	// Link all new generated relay point Selected button to an action
	$('.PS_MRSelectRelayPointButton').click(function()
	{
		// Unselect all previous selection (normaly juste one)
		$('.PS_MRFloatRelayPointSelected').each(function()
		{
			$(this).attr('class', 'PS_MRFloatRelayPointSelecteIt');
			$(this).children('a').text(PS_MRTranslationList['Select']);
		});
		// Make the Selection
		$(this).html(PS_MRTranslationList['Selected']);
		$(this).parent().attr('class', 'PS_MRFloatRelayPointSelected');
		
		// Get the info about the relay point (relayPoint_RelayPointNumber_IdCarrier)
		tab = $(this).parent().parent().attr('id').split('_');
		
		// Store Separated data for the ajax query
		if (tab.length == 3)
		{
			relayPointNumber = tab[1];
			id_carrier = tab[2];
			PS_MRAddSelectedRelayPointInDB(relayPointNumber, id_carrier);
		}
	});	
}


// Display the relay point fetched
function PS_MRDisplayRelayPoint(json, blockContent, carrier_id)
{
	if (typeof json != 'undefined' && typeof blockContent != 'undefined')
	{
		numberDisplayed = 0;
		
		// Disable Gmap for IE user
		if (!$.browser.msie)
		PS_MRCreateGmap(carrier_id);
		blockContent.fadeOut('fast', function()
		{
			$(this).children('td').html('');
			for (relayPoint in json.success)
			{
				// Check if the the content wasn't already displayed
				contentBlockid =  'relayPoint_' + json.success[relayPoint].Num + '_' + carrier_id;
				if (!$('#' + contentBlockid).size())
				{
					// Set translation if a preselection exist
					var BtTranslation = (PS_MRPreSelectedRelay == json.success[relayPoint].Num) ?
						PS_MRTranslationList['Selected'] : PS_MRTranslationList['Select'];
					
					var classSelection = (PS_MRPreSelectedRelay == json.success[relayPoint].Num) ?
						'PS_MRFloatRelayPointSelected' : 'PS_MRFloatRelayPointSelecteIt';
					
					$('<div class="PS_MRRelayPointInfo clearfix" id="' + contentBlockid + '"> \
						<img src="' + _PS_MR_MODULE_DIR_ + 'logo_hd.png" /> \
						<p><b>' + json.success[relayPoint].LgAdr1 + '</b><br /> ' +  json.success[relayPoint].LgAdr3
						+ ' - ' + json.success[relayPoint].CP + ' - ' + json.success[relayPoint].Ville
						+ ' ' + json.success[relayPoint].Pays + '</p> \
						<div class="' + classSelection + '"> \
							<a class="PS_MRSelectRelayPointButton">' + BtTranslation  + '</a> \
						</div> \
					</div>').appendTo($(this).children('td'));
					
					// Store all the object content to prevent an ajax request
					relayPointDataContainers[json.success[relayPoint].Num] = json.success[relayPoint];
					++numberDisplayed;
					// Display popup for IE user
					if (!$.browser.msie)
					PS_MRAddGMapMarker(carrier_id, json.success[relayPoint].Num, contentBlockid);
					else
						$('#' + contentBlockid).children('p').click(function() {
							PS_MROpenPopupDetail(json.success[relayPoint].permaLinkDetail);
						});
				}
			}
			PS_MRHandleSelectedRelayPoint();
			$(this).fadeIn('fast');
		});
	}
}

function PS_MRDisplayErrorRelayPoint(errorList, blockContent)
{
	
	blockContent.fadeOut('fast', function()
	{
		$(this).children('td').html('');
		for (numError in errorList)
		{
			$('<div class="error">' + errorList[numError] + '</div>').appendTo($(this).children('td'));
		}
		$(this).fadeIn('fast');
	});
}

// Fetch the relay point
function PS_MRFetchRelayPoint(carrierSelected)
{
	carrier_id = carrierSelected.val();
	
	// Block is an input, we need the 'tr' element
	blockTR = carrierSelected.parent().parent();
	// Add a new line to the table after the clicked parent element
	blockTR.after(' \
		<tr class="PS_MRSelectedCarrier" id="PS_MRSelectedCarrier_' + carrier_id + '"> \
			<td colspan="4"><div> \
				<img src="' + _PS_MR_MODULE_DIR_ + 'images/loader.gif" /> \
			</div> \
		</td></tr>');
	
	fetchingRelayPoint[carrier_id] = $('#PS_MRSelectedCarrier_' + carrier_id);
	MRjQuery.ajax(
	{
		type: 'POST',
		url: _PS_MR_MODULE_DIR_ + 'ajax.php',
		data: {'method' : 'MRGetRelayPoint',
					'id_carrier' : carrier_id,
					'mrtoken' : mrtoken},
		dataType: 'json',
		success: function(json) 
		{
			if (json && json.error && json.error.length)
				PS_MRDisplayErrorRelayPoint(json.error, $('#PS_MRSelectedCarrier_' + carrier_id));
			else if (json && json.success)
				PS_MRDisplayRelayPoint(json, $('#PS_MRSelectedCarrier_' + carrier_id), carrier_id);					
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			// Put debug to see error detail
		}
	});
}

// Display the relay point of a selected Carrier and keep fetched data
// in the html page (cache)
function PS_MRGetRelayPoint(carrierSelected)
{
	carrier_id = carrierSelected.val();
	
	// Init back the inital view, hide existing element, (keep cached)
	element = 0;
	totalElement = $('.PS_MRSelectedCarrier').size();
	
	// Check if the element has already been fetched
	if (totalElement)
		// It Works like a foreach
		$('.PS_MRSelectedCarrier').fadeOut('fast', function()
		{
			if ((element + 1) >= totalElement)
			{
				// Check if the user already clicked and if the process is done
				if (typeof fetchingRelayPoint[carrier_id] != 'undefined')
				{
					fetchingRelayPoint[carrier_id].fadeIn('fast');
					return ;
				}
				// If the element isn't cached, we fetch it
				PS_MRFetchRelayPoint(carrierSelected);
			}
			++element;
		});
	else
		PS_MRFetchRelayPoint(carrierSelected);
}

/*
** Create the Gmap Block and cache the js object for the carrier
*/
function PS_MRCreateGmap(id_carrier)
{
	// This has been write this way because it needed to have a known block
	// present everytime in the page. Body is the only one sure.
	// It's an hidden block it's replaced in the good block when user select his
	// own carrier
	$('body').prepend('\
		<tr id="PS_MRGmapDefaultPosition_' + id_carrier + '" class="PS_MRGmapDefaultPosition">\
			<td colspan="4"> \
				<div id="PS_MRGmap_' + id_carrier + '" class="PS_MRGmapStyle">\
		</div></td</tr>');
	 $('#PS_MRGmap_' + id_carrier).gmap3(
		{
			action: 'init',
			callback: function(result)
			{
				GmapList[id_carrier] = $(this);
				// Can't set the display to none by default (bug due to
				// navigator that tell to google that content size = 0
				//$(this).toggle('fast');
			}
		}
	);
}

/*
** Resize the map when the div got changement about dimension / position and displaying
*/
function PS_MRGmapResizeEvent($map)
{
	gmap = $map.gmap3({action:'get'});	
	google.maps.event.trigger(gmap, 'resize');
}

/*
** Move the view of the gmap to a marker 
** liable to the relay point 
*/
function PS_MRGmapPlaceViewOnMarker($map, marker, relayNum)
{
	$map.gmap3(
	{
			action:'panTo',
			args:[marker.position],
			callback: function()
			{
        PS_MRDisplayClickedGmapWindow(marker, relayNum, $map);
        
        // Make dancing markers in Firefox will use the CPU to 100 %
        if (!$.browser.mozilla)
				(function(m)
				{
      		setTimeout(function() 
      		{
          	m.setAnimation(google.maps.Animation.BOUNCE);
        	}, 200);
    		})(marker);
			//	marker.setAnimation(google.maps.Animation.BOUNCE);
			}
	});
}

/*
** Stop all dancing marker of the current selected carrier
*/
function PS_MRStopDancingMarkers(currentMarkerList)
{
	for (markerNumber in currentMarkerList)
		if (currentMarkerList[markerNumber] != undefined)
			if (currentMarkerList[markerNumber].getAnimation() != null)
		 		currentMarkerList[markerNumber].setAnimation(null);
}

/*
** Display the Gmap of the selected relay point 
*/
function PS_MRDisplayGmap(contentBlockid, $map)
{	
	tab = contentBlockid.split('_');
	relayPointNumber = tab[1];
	id_carrier = tab[2];
	
	// Stop the dancing marker of the current carrier
	PS_MRStopDancingMarkers(markerList[id_carrier]);
	if ($('#PS_MRGmap_' + id_carrier).css('display') == 'none')
	{
		$('#' + contentBlockid).after($('#PS_MRGmap_' + id_carrier));
		$('#PS_MRGmap_' + id_carrier).slideDown('fast', function()
		{
			PS_MRGmapResizeEvent($map);
			PS_MRGmapPlaceViewOnMarker($map, markerList[id_carrier][relayPointNumber], relayPointNumber);
		});
	}
	else
	{
		previousElem = $('#PS_MRGmap_' + id_carrier).prev();	
		//$('#' + contentBlockid).after($('#PS_MRGmap_' + id_carrier));	
		$('#PS_MRGmap_' + id_carrier).toggle('fast', function()
		{
			if (previousElem.attr('id') != contentBlockid)
			{
				$('#' + contentBlockid).after($(this));	
				$('#PS_MRGmap_' + id_carrier).slideDown('fast', function()
				{
					PS_MRGmapPlaceViewOnMarker($map, markerList[id_carrier][relayPointNumber], relayPointNumber);
				});
			}
		});
	}
}

/*
** Generate an html block to display the opening hours details
*/
function PS_MRGetTimeRelayDetail(relayInfo)
{
	onClick = 'onClick="PS_MROpenPopupDetail(\'' + relayInfo.permaLinkDetail + '\')"';
	
	var html = ' \
		<div class="PS_MRGmapBulbe"> \
			<img src="' + _PS_MR_MODULE_DIR_ + 'logo_hd.png" width="10%" style="float:left;" /> \
			<p><b>' + relayInfo.LgAdr1 + '</b><br /> ' +  relayInfo.LgAdr3
			+ ' - ' + relayInfo.CP + ' - ' + relayInfo.Ville
			+ ' ' + relayInfo.Pays + '</p> \
			<a href="javascript:void(0)" ' + onClick + '>' + PS_MRTranslationList['moreDetails'] + '</a> \
		</div>';
	return html;
}

/*
** Call a MondialRelay page into a popup
*/
function PS_MROpenPopupDetail(url)
{
	window.open(url, 'MondialRelay', 
		'height=200, width=400, top=100, left=100, toolbar=no, menubar=yes, \
		location=no, resizable=yes, scrollbars=no, status=no');
}

/*
** Display the gmap windows selected by the user
*/
function PS_MRDisplayClickedGmapWindow(marker, relayNum, mapObject)
{	
	if (last_gmap_info_clicked.length)
  {
   	// Close the last opening window in gmap
	  if ((lastWin = mapObject.gmap3({action:'get', name:'infowindow', tag:last_gmap_info_clicked})))
	  	lastWin.close();
	}

	// Open the selected detail window
 	map = mapObject.gmap3('get');
  if ((currentWindow = mapObject.gmap3({action:'get', name:'infowindow', tag:relayNum})))
		currentWindow.open(map, marker);
  last_gmap_info_clicked = relayNum;
}

/*
** Add a new Marker to a Gmap for a carrier using the 
** relay point information 
*/
function PS_MRAddGMapMarker(id_carrier, relayPointNumber, contentBlockid)
{
	// Check if the gmap has been properly created
	if (GmapList[id_carrier] == undefined)
		return ;
	var relayInfo = relayPointDataContainers[relayPointNumber];
	var detailContentHtml = PS_MRGetTimeRelayDetail(relayInfo);
	
	// Add Marker to the map
	var fullFormatedAddress = relayInfo.LgAdr1 + ' ' + relayInfo.LgAdr3 + ' ' + 
		relayInfo.CP + ' ' + relayInfo.Ville + ' ' + relayInfo.Pays;
	GmapList[id_carrier].gmap3(
	{ 
		action: 'addMarker',
		address: fullFormatedAddress,
		tag:relayInfo.Num,
		map:
		{
    	center: true,
    	zoom: 14
  	},
  	marker: {
  		events:
      {
       	click:function(marker, event, data)
       	{
       		PS_MRDisplayClickedGmapWindow(marker, relayInfo.Num, $(this));
        }
      },
     	callback: function(marker)
			{
				if (marker)
				{
					// Check if the a marker list exist for the carrier,
					if (markerList[id_carrier] == undefined)
						markerList[id_carrier] = new Object();
	
					// Store the marker in the markerList of the carrier
					markerList[id_carrier][relayPointNumber] = marker;
					
					// Link all relay point line info to an action
					$('#' + contentBlockid).children('p').click(function()
					{
						PS_MRDisplayGmap($(this).parent().attr('id'), GmapList[id_carrier]);
					});
					return true;
				}
				else
					$('#' + contentBlockid).children('p').click(function()
					{
						PS_MROpenPopupDetail(relayInfo.permaLinkDetail);
					});
			}
		},
		infowindow:
		{
			options: {content:detailContentHtml},
			tag:relayInfo.Num,
			callback: function(infowindow) {
			
				var windowList = $(this).gmap3({action:'get', name:'infowindow', all:true});
        $.each(windowList, function(i, elem) {
          elem.close();
				});
			}
		}	
	});
	return false;
}	


$(document).ready(function()
{
	$('#form').submit(function()
	{
		return PS_MRCheckSelectedRelayPoint();
	});
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
	$('#PS_MRDisplayPersonalizedOptions').click(function()
	{
		$('#PS_MRAdvancedSettings').toggle('fast');
});
});


