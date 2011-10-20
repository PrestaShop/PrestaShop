<div class="" id="step1">
			<h4 class="tab">1. {l s='Info.'}</h4>
			<script type="text/javascript">
				$(document).ready(function() {
					updateCurrentText();
					updateFriendlyURL();
					$.ajax({
						url: "ajax-tab.php",
						cache: false,
						dataType: "json",
						data: {
							ajaxProductManufacturers:"1",
							ajax : '1',
							token : "{$token}",
							controller : "AdminProducts",
							action : "productManufacturers",
						},
						success: function(j) {
							var options = $("select#id_manufacturer").html();
							if (j)
							for (var i = 0; i < j.length; i++)
								options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
							$("select#id_manufacturer").replaceWith("<select id=\"id_manufacturer\">"+options+"</select>");
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
						}

					});
					$.ajax({
						url: "ajax-tab.php",
						cache: false,
						dataType: "json",
						data: {
							ajaxProductSuppliers:"1",
							ajax : '1',
							token : "{$token}",
							controller : "AdminProducts",
							action : "productSuppliers",
						},
						success: function(j) {
							var options = $("select#id_supplier").html();
							if (j)
							for (var i = 0; i < j.length; i++)
								options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
							$("select#id_supplier").replaceWith("<select id=\"id_supplier\">"+options+"</select>");
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							$("select#id_supplier").replaceWith("<p id=\"id_supplier\">[TECHNICAL ERROR] ajaxProductSuppliers : "+textStatus+"</p>");
						}

					});
					if ($('#available_for_order').is(':checked')){
						$('#show_price').attr('checked', 'checked');
						$('#show_price').attr('disabled', 'disabled');
					}
					else {
						$('#show_price').attr('disabled', '');
					}
				});
			</script>
			<b>{l s='Product global information'}</b>&nbsp;-&nbsp;
		<h3>{l s='Current product:'}<span id="current_product" style="font-weight: normal;">{l s='no name'}</span></h3>
		<script type="text/javascript">
			{$combinationImagesJs}
			$(document).ready(function(){
				$('#id_mvt_reason').change(function(){
					updateMvtStatus($(this).val());
				});
				updateMvtStatus($(this).val());
			});
			function updateMvtStatus(id_mvt_reason)
			{
				if (id_mvt_reason == -1)
					return $('#mvt_sign').hide();
				if ($('#id_mvt_reason option:selected').attr('rel') == -1)
					$('#mvt_sign').html('<img src="../img/admin/arrow_down.png" /> {l s='Decrease your stock'}');
				else
					$('#mvt_sign').html('<img src="../img/admin/arrow_up.png" /> {l s='Increase your stock'}');
				$('#mvt_sign').show();
			}
		</script>

{$content}




