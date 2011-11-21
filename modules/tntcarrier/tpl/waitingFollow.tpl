<script type="text/javascript">
	$(document).ready(function() {
		var children = $('#followPackage').children();
		$('#waitingDiv').html("Chargement du suivi colis <img src='./img/loadingAnimation.gif' alt='wait'/>");
		for (var i = 0; i < children.length; i++)
		{
			$("#"+children[i].id).load(
				"./modules/tntcarrier/follow.php?code="+children[i].id.substr(14),
			function(response, status, xhr) 
				{
					if (status == "error") 
						$("#followPackage").html(xhr.status + " " + xhr.statusText);
					if (i == children.length)
						$('#waitingDiv').html("");
				}
			);
		}
	/**/
	});
</script>
<div id="followPackage" style="clear:both">
	{foreach from=$numbers item=v}
	<div id="followPackage_{$v.shipping_number}">
	</div>
	{/foreach}
</div>
<div id="waitingDiv"></div>