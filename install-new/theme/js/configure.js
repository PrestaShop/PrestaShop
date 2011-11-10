$(document).ready(function()
{
	// Change logo
	$('#fileToUpload').bind('change', upload_logo);
	
	// When a country is changed
	$('#infosCountry').change(function()
	{
		var iso = $(this).val();

		// Get timezone by iso
		$.ajax({
			url: 'index.php',
			data: 'timezoneByIso=true&iso='+iso,
			dataType: 'json',
			cache: true,
			success: function(json)
			{
				if (json.success)
					$('#infosTimezone').val(json.message);
			}
		});

		// Load associated partners
		//load_partners(iso);
	});
	
	//if (default_iso)
	//	load_partners(default_iso);
});

/**
 * Upload a new logo
 */
function upload_logo()
{
	$.ajaxFileUpload(
	{
		url: 'index.php?uploadLogo=true',
		secureuri: false,
		fileElementId: 'fileToUpload',
		dataType: 'json',
		success: function(json)
		{
			if (typeof(json.success) == 'undefined')
				return ;

			$("#uploadedImage").slideUp('slow', function()
			{
				if (!json.success)
					$('#resultInfosLogo').html(json.message).addClass('errorBlock').show();
				else
				{
					$(this).attr('src', ps_base_uri+'img/logo.jpg?'+(new Date()))
					$(this).show('slow');
					$('#resultInfosLogo').html('').removeClass('errorBlock').hide();
				}
				
				$('#fileToUpload').bind('change', upload_logo);
			});
		},
		error: function()
		{
			$('#uploadedImage').attr('src', ps_base_uri+'img/logo.jpg?'+(new Date()));
			$('#resultInfosLogo').html('').addClass('errorBlock');
		}
	});
};

/**
 * Load partners for a given country
 * 
 * @param string iso
 */
function load_partners(iso)
{
	$.ajax({
		url: 'index.php',
		data: 'getPartners=true&iso='+iso,
		dataType: 'json',
		cache: false,
		success: function(json)
		{
			if (json.success)
			{
				// Display partner HTML
				$('#benefitsBlock').html(json.message).show();
				
				// Add event on partner checkbox to display fields if it's checked
				$('.preinstall_partner').click(function()
				{
					var name = $(this).attr('name');
					var partner_id = name.substr(8, name.length - 9);
					
					if ($(this).attr('checked'))
						load_partner_fields(partner_id, iso);
					else
						$('#partner_fields_'+partner_id).html('').hide();
				});
			}
			else
				$('#benefitsBlock').html('');
		}
	});
}

/**
 * Display partner fields
 * 
 * @param string partner_id Key of partner
 */
function load_partner_fields(partner_id, iso)
{
	$.ajax({
		url: 'index.php',
		data: 'getPartnersFields=true&partner_id='+partner_id+'&iso='+iso,
		dataType: 'json',
		cache: false,
		success: function(json)
		{
			if (json.success)
				$('#partner_fields_'+partner_id).html(json.message).show();
		}
	});
}