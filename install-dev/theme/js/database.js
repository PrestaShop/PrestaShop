/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(function()
{
	// Check rewrite engine availability
	$.ajax({
		url: 'sandbox/anything.php',
		success: function(value) {
			$('#rewrite_engine').val(1);
		}
	});

	// Check database configuration
	$('#btTestDB').on('click', function()
	{
		$("#dbResultCheck")
			.removeClass('errorBlock')
			.removeClass('okBlock')
			.addClass('waitBlock')
			.html('&nbsp;')
			.slideDown('slow');
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
					.removeClass('waitBlock')
					.removeClass((json.success) ? 'errorBlock' : 'okBlock')
					.html(json.message)
			},
            error: function(xhr)
            {
            	var str = xhr.responseText || '';
            	var isHtml = /<[a-z][\s\S]*>/i.test(str);
            	var detail = isHtml
            		? ('HTTP ' + xhr.status + ': expected JSON, got HTML (' + str.length + ' bytes). First chars: '
            			+ $('<div>').text(str.replace(/\s+/g, ' ').slice(0, 180)).html())
            		: str;

                $("#dbResultCheck")
                    .addClass('errorBlock')
					.removeClass('waitBlock')
                    .removeClass('okBlock')
                    .html('An error occurred:<br /><br />' + detail)
            }
		});
	});
});

function bindCreateDB()
{
	// Attempt to create the database
	$('#btCreateDB').on('click', function()
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
