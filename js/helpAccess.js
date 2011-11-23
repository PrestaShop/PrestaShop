$(document).ready(function(){
	if($('.help-context'))
	{
		$.ajax({
			type: 'POST',
			url: 'index.php',
			data: {
				'ajax' : '1',
				'action' : 'helpAccess',
				'item' : class_name,
				'isoUser' : iso_user,
				'country' : country_iso_code,
				'version' : _PS_VERSION_
			},
			async : true,
			success: function(msg) {
				if(msg.status == 'ok' && msg.content != 'none')
				{
					$(".help-context").html(msg.content);
					$(".help-context").fadeIn("slow").show();
				}
			}
		});
	}
});