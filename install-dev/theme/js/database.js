$(document).ready(function()
{
	// Check database configuration
	$('#btTestDB').click(function()
	{
		$("#dbResultCheck").slideUp('slow');
		$.ajax({
			url: 'index.php',
			data: {
                'checkDb': 'true',
                'dbServer': $('#dbServer').val(),
                'dbName': $('#dbName').val(),
                'dbLogin': $('#dbLogin').val(),
                'dbPassword': $('#dbPassword').val(),
                'dbEngine': $('#dbEngine').val(),
                'db_prefix': $('#db_prefix').val(),
                'clear': $('#db_clear').prop('checked') ? '1' : '0'
            },
			dataType: 'json',
			cache: false,
			success: function(json)
			{
				$("#dbResultCheck")
					.addClass((json.success) ? 'okBlock' : 'errorBlock')
					.removeClass((json.success) ? 'errorBlock' : 'okBlock')
					.html(json.message)
					.slideDown('slow');
			},
            error: function(xhr)
            {
                $("#dbResultCheck")
                    .addClass('errorBlock')
                    .removeClass('okBlock')
                    .html('An error occurred:<br /><br />' + xhr.responseText)
                    .slideDown('slow');
            }
		});
	});
});

function bindCreateDB()
{
	// Attempt to create the database
	$('#btCreateDB').click(function()
	{
		$("#dbResultCheck").slideUp('fast');
		$.ajax({
			url: 'index.php',
			data: {
                'createDb': 'true',
                'dbServer': $('#dbServer').val(),
                'dbName': $('#dbName').val(),
                'dbLogin': $('#dbLogin').val(),
                'dbPassword': $('#dbPassword').val()
            },
			dataType: 'json',
			cache: false,
			success: function(json)
			{
				$("#dbResultCheck")
					.addClass((json.success) ? 'okBlock' : 'errorBlock')
					.removeClass((json.success) ? 'errorBlock' : 'okBlock')
					.html(json.message)
					.slideDown('slow');
			},
            error: function(xhr)
            {
                $("#dbResultCheck")
                    .addClass('errorBlock')
                    .removeClass('okBlock')
                    .html('An error occurred:<br /><br />' + xhr.responseText)
                    .slideDown('slow');
            }
		});
	});
}