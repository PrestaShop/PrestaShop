	<link href="{$timetable_css}" rel="stylesheet" type="text/css" media="all" />
	<script type="text/javascript">
	// <![CDATA[
		var djl_calendar = new Array({foreach from=$dates item=dateItem name=datesLoop}{if !$smarty.foreach.datesLoop.first}, {/if}new Array("{$dateItem.label}", "{$dateItem.value}", {$dateItem.start_hour}, {$dateItem.stop_hour}){/foreach}	);
		var djlAjaxStoreUrl = '{$module_dir}' + 'ajaxStoreShippingInfo.php' ;
		var djlIsOpc = false ;
		{if $opc}
			djlIsOpc = true ;
		{/if}

		function DJLupdateCarrierSelectionAndGift() {
			if (djlIsOpc) {
				djlUpdateCarrierInfo() ;
				updateCarrierSelectionAndGift();
			}
		}
	//]]>
	</script>

	{literal}
	<script type="text/javascript">
	// <![CDATA[
			$(document).ready(function() {
				$('input[name="id_carrier"]').each(function(idx, elt) {
					if ($(this).parent().hasClass("dejala")) {
						$(this).click(function() {
							$('div#djl_shipping_pref').show('fast') ;
							selectDay();
						});
						if ($(this).is(':checked')) {
							$(this).click() ;
					}
					else {
							$('div#djl_shipping_pref').hide() ;
						}
					}
					else {
						$(this).click(function() {
							$('div#djl_shipping_pref').hide('fast') ;
						});
					}
				}) ;

			});

			function hideUnusedHours(selectedDay) {
				entries = jQuery.grep(djl_calendar, function (elt, idx) { if (elt[1] == selectedDay) return true; }) ;
				$('#shipping_hours div input').parent().addClass("hidden") ;
				$(entries).each(function(idxEach, eltEach) {
					$(jQuery.grep($('#shipping_hours div input'), function (elt, idx) { if ($(elt).val() >=eltEach[2] && $(elt).val() <=eltEach[3]) return true; })).parent().removeClass("hidden");
				}) ;
			}

			function selectDay() {
				$('#shipping_dates div input').parent().removeClass("djl_active") ;
				if ($('#shipping_dates div input:checked').length == 0) {
					$('#shipping_dates div input').val([$($('#shipping_dates div input')[0]).val()]) ;
				}
				$('#shipping_dates div input:checked').parent().addClass("djl_active") ;
				
				hideUnusedHours($('#shipping_dates div input:checked').val()) ;

				if ($('#shipping_hours div input:checked').parent().is(':visible')) {
					$('#shipping_hours div input').val([$($('#shipping_hours div input:checked')[0]).val()]) ;
				}
				else {
					$('#shipping_hours div input').val([$($('#shipping_hours div input:visible')[0]).val()]) ;
				}

				selectHour() ;
			}


			function selectHour() {
				$('#shipping_hours div input').parent().removeClass("djl_active") ;
				$('#shipping_hours div input:checked').parent().addClass("djl_active") ;
				if (djlIsOpc) {
					djlUpdateCarrierInfo();
				}
			}

			function djlUpdateCarrierInfo() {
				$.ajax({
			           type: 'POST',
			           url: djlAjaxStoreUrl,
			           async: true,
			           cache: false,
			           dataType : "json",
			           data: {
							'dejala_id_carrier' : $('input[name=dejala_id_carrier]').val(),
							'dejala_id_product' : $('input[name=dejala_id_product]').val(),
							'shipping_day' : $('input[name=shipping_day]:checked').val(),
							'shipping_hour' : $('input[name=shipping_hour]:checked').val(),
							'id_carrier' : $('input[name=id_carrier]:checked').val()
			           },
			           success: function(jsonData)
			           {
							if (jsonData.hasError)
							{
								var errors = '';
								for(error in jsonData.errors)
									//IE6 bug fix
									if(error != 'indexOf')
										errors += jsonData.errors[error] + "\n";
								alert(errors);
							}
						},
			           error: function(XMLHttpRequest, textStatus, errorThrown) {alert("TECHNICAL ERROR: unable to update Dejala shipping information \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);}
				});
			}
		//]]>
		</script>
	{/literal}
	
	{foreach from=$djlCarriers item=carrier name=myLoop}
				<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{else}item{/if}">
					<td class="carrier_action radio dejala">
						<input type="hidden" name="dejala_id_carrier" value="{$carrier.id_carrier|intval}"/>
						<input type="hidden" name="dejala_id_product" value="{$product.id|intval}"/>
						<input type="radio" name="id_carrier" value="{$carrier.id_carrier|intval}" id="id_carrier{$carrier.id_carrier|intval}" {if $carrier.id_carrier == $djlCarrierChecked}checked="checked"{/if} onclick="javascript:DJLupdateCarrierSelectionAndGift();"/>
					</td>
					<td class="carrier_name">
						<label for="id_carrier{$carrier.id_carrier|intval}">
							{if $carrier.img}<img src="{$carrier.img|escape:'htmlall':'UTF-8'}" alt="{$carrier.name|escape:'htmlall':'UTF-8'}" />{else}{$carrier.name|escape:'htmlall':'UTF-8'}{/if}
						</label>
					</td>
					<td class="carrier_infos">{$carrier.info|escape:'htmlall':'UTF-8'}</td>
					<td class="carrier_price">
						{if $carrier.price}
							<span class="price">
								{if $priceDisplay == 1}{convertPrice price=$carrier.price_tax_exc}{else}{convertPrice price=$carrier.price}{/if}
							</span>
							{if $priceDisplay == 1} {l s='(tax excl.)' mod='dejala'}{else} {l s='(tax incl.)' mod='dejala'}{/if}
						{else}
							{l s='Free!' mod='dejala'}
						{/if}
					</td>
				</tr>
	{/foreach}