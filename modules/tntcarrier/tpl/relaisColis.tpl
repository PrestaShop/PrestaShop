<script type="text/javascript">
	function getAjaxRelais(id)
	{
		$("#relaisColisCarrier").load(
			"./modules/tntcarrier/relaisColis/relaisColis.php?id_carrier="+id,
			function(response, status, xhr) 
			{
				if (status == "error") 
					$("#relaisColisCarrier").html(xhr.status + " " + xhr.statusText);
			}
		);
		$("#relaisColisCarrier").slideDown('slow');
	}
	
	$("input[name='id_carrier']").click(function() {
	getAjaxRelais($("input[name='id_carrier']:checked").val());
	});
</script>
<div id="relaisColisCarrier" style="display:none">
</div>
<input type="hidden" id="cartRelaisColis" value="{$id_cart}" name="cartRelaisColis" />