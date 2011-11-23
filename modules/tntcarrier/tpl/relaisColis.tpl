<script type="text/javascript">
    $("input[name='id_carrier']").click(function() {
    getAjaxRelais($("input[name='id_carrier']:checked").val());
    });

	function getAjaxRelais(id)
	{
       /* var parent = document.getElementById("id_carrier"+id).parentNode.parentNode.parentNode;

        if (document.getElementById("tr_carrier_relais"))
            {
                var node = document.getElementById("tr_carrier_relais");
                var father = node.parentNode;
                father.removeChild(node);
                return;
            }
        var tr = document.createElement("tr");
        tr.innerHTML = "<td colspan='4' style='display:none' id='tr_carrier_relais'></td>";
        
        parent.insertBefore(tr, document.getElementById("id_carrier"+id).parentNode.parentNode.nextSibling);
*/
		$.get(
			"./modules/tntcarrier/relaisColis/relaisColis.php?id_carrier="+id,
			function(response, status, xhr) 
			{
				if (status == "error") 
					$("#tr_carrier_relais").html(xhr.status + " " + xhr.statusText);
                if (status == 'success')
                    $("#tr_carrier_relais").html(response);
			}
		);
		$("#tr_carrier_relais").slideDown('slow');
	}
	
</script>
<div id="tr_carrier_relais" style="display:none">
</div>
<input type="hidden" id="cartRelaisColis" value="{$id_cart}" name="cartRelaisColis" />