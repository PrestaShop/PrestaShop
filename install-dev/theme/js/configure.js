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
					$('#infosTimezone').val(json.message).trigger("liszt:updated");
			}
		});
	});
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
