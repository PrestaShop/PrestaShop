$(document).ready(function()
{	
	bindStateInputAndUpdate();
});

function bindStateInputAndUpdate()
{
	$('select#id_country').change(function(){
		updateState();
		updateNeedIDNumber();
		updateZipCode();
	});
	
	if ($('select#id_country_invoice').length != 0)
	{
		$('select#id_country_invoice').change(function(){
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode();
		});
		if ($('select#id_country_invoice:visible').length != 0)
		{
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		}
	}
	
	updateState();
	updateNeedIDNumber();
	updateZipCode();
}

function updateState(suffix)
{
	$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')+' option:not(:first-child)').remove();
	var states = countries[$('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val()];
	if(typeof(states) != 'undefined')
	{
		$(states).each(function (key, item){
			$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')).append('<option value="'+item.id+'"'+ (idSelectedCountry == item.id ? ' selected="selected"' : '') + '>'+item.name+'</option>');
		});
		
		$('p.id_state'+(suffix !== undefined ? '_'+suffix : '')+':hidden').slideDown('slow');
	}
	else
		$('p.id_state'+(suffix !== undefined ? '_'+suffix : '')).hide();
		
}

function updateNeedIDNumber(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());

	if ($.inArray(idCountry, countriesNeedIDNumber) >= 0)
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).slideDown('slow');
	else
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}

function updateZipCode(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());
	
	if (countriesNeedZipCode[idCountry] != 0)
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).slideDown('slow');
	else
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}
