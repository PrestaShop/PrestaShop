$(document).ready(function()
{	
	bindStateInputAndUpdate();
});

function bindStateInputAndUpdate()
{
	$('.id_state, .dni, .postcode').css({'display':'none'});
	updateState();
	updateNeedIDNumber();
	updateZipCode();

	$('select#id_country').change(function(){
		updateState();
		updateNeedIDNumber();
		updateZipCode();
	});

	if ($('select#id_country_invoice').length !== 0)
	{
		$('select#id_country_invoice').change(function(){   
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		});
		updateState('invoice');
		updateNeedIDNumber('invoice');
		updateZipCode('invoice');
	}
}

function updateState(suffix)
{
	$('select#id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '')+' option:not(:first-child)').remove();
	var states = countries[$('select#id_country'+(typeof suffix !== 'undefined' ? '_' + suffix : '')).val()];
	if(typeof states !== 'undefined')
	{
		$(states).each(function (key, item){
			$('select#id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).append('<option value="' + item.id + '"' + (idSelectedCountry === item.id ? ' selected="selected"' : '') + '>' + item.name + '</option>');
		});
		$('.id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '') + ':hidden').fadeIn('slow');;
	}
	else
		$('.id_state' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).fadeOut('fast');
}

function updateNeedIDNumber(suffix)
{
	var idCountry = parseInt($('select#id_country' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).val());
	if ($.inArray(idCountry, countriesNeedIDNumber) >= 0)
		$('.dni' + (typeof suffix !== 'undefined' ? '_' + suffix : '') + ':hidden').fadeIn('slow');
	else
		$('.dni' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).fadeOut('fast');
}

function updateZipCode(suffix)
{
	var idCountry = parseInt($('select#id_country' + (typeof suffix !== 'undefined' ? '_' + suffix : '')).val());

	if (typeof countriesNeedZipCode[idCountry] !== 'undefined')
		$('.postcode' + (typeof suffix !== 'undefined' ? '_' + suffix : '') + ':hidden').fadeIn('slow');
	else
		$('.postcode'+(typeof suffix !== 'undefined' ? '_' + suffix : '')).fadeOut('fast');
}
