
	<div class="hint" style="display:block; position:'auto';">
		<p>{l s='This interface allows you to manage the available quantities for sale of the current product and its combinations on the current shop.'}</p>
		<p>{l s='You can manually specify the quantities for the product / each product combinations, or choose to automatically determine these quantities based on your stock.'}</p>
		<p>{l s='In this case, the quantities correspond to the quantitites of the real stock in the warehouses associated to the current shop.'}</p>
	</div>
	<br />
	<table cellpadding="5">
		<tbody>
			<tr>
				<td colspan="2">
					<b>{l s='Available quantities for sale'}</b>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="separation"></div>
	<div class="warn" id="available_quantity_ajax_msg" style="display: none;"></div>
	<div class="error" id="available_quantity_ajax_error_msg" style="display: none;"></div>
	<div class="conf" id="available_quantity_ajax_success_msg" style="display: none;"></div>

	<table cellpadding="5" style="width:100%">
		<tbody>
			<tr>
				<td valign="top" style="vertical-align:top;">
					<input {if $product->depends_on_stock == 1 && $stock_management_active == 1}checked="checked" {/if} {if $stock_management_active == 0}disabled="disabled" {/if} type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_1" value="1"/>
					<label style="float:none;font-weight:normal" for="depends_on_stock_1">{l s='Available quantities for current product and its combinations are based on stock in the warehouses'} {if $stock_management_active == 0}&nbsp;-&nbsp;<b>{l s='Not possible if stock management is not enabled'}</b>{/if}</label>
					<br /><br />
				</td>
			</tr>
			<tr>
				<td valign="top" style="vertical-align:top;">
					<input {if $product->depends_on_stock == 0 || $stock_management_active == 0}checked="checked" {/if} type="radio" name="depends_on_stock" class="depends_on_stock" id="depends_on_stock_0" value="0"/>
					<label style="float:none;font-weight:normal" for="depends_on_stock_0">{l s='I want to specify available quantities manually, and manage my stock independently'}</label>
					<br /><br />
				</td>
			</tr>
			<tr>
				<td valign="top" style="text-align:center;vertical-align:top;">
					<table class="table" cellpadding="0" cellspacing="0" style="width:60%;margin-left:20%;">
						<thead>
							<tr>
								<th style="width:200px;">{l s='Quantity'}</th>
								<th>{l s='Designation'}</th>
							</tr>
						</thead>
						<tbody>
						{foreach from=$attributes item=attribute}
							<tr>
								<td  class="available_quantity" id="qty_{$attribute['id_product_attribute']}">
									<span>{$available_quantity[$attribute['id_product_attribute']]}</span>
									<input type="text" value="{$available_quantity[$attribute['id_product_attribute']]}"/>
								</td>
								<td>{$product_designation[$attribute['id_product_attribute']]}</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</td>
			</tr>
			<tr id="when_out_of_stock">
				<td>
					<table style="margin-top: 15px;">
						<tbody>
							<tr>
								<td class="col-left"><label>{l s='When out of stock:'}</label></td>
								<td style="padding-bottom:5px;">
									<input {if $product->out_of_stock == 0}checked="checked" {/if} id="out_of_stock_1" type="radio" checked="checked" value="0" class="out_of_stock" name="out_of_stock">
									<label id="label_out_of_stock_1" class="t" for="out_of_stock_1">{l s='Deny orders'}</label>
									<br>
									<input {if $product->out_of_stock == 1} 'checked="checked" {/if} id="out_of_stock_2" type="radio" value="1" class="out_of_stock" name="out_of_stock">
									<label id="label_out_of_stock_2" class="t" for="out_of_stock_2">{l s='Allow orders'}</label>
									<br>
									<input {if $product->out_of_stock == 2} 'checked="checked" {/if} id="out_of_stock_3" type="radio" value="2" class="out_of_stock" name="out_of_stock">
									<label id="label_out_of_stock_3" class="t" for="out_of_stock_3">
										Default:
										<i>Deny orders</i>
										{assign var=confirm value="Are you sure you want to delete entered product information?"}
										<a onclick="return confirm(' {$confirm} ');"
											href="index.php?tab=AdminPPreferences&token={$token_preferences}">
												{l s='as set in Preferences'}
										</a>
									</label>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

<script type="text/javascript">
	var showAjaxError = function(msg)
	{
		$('#available_quantity_ajax_error_msg').html(msg);
		$('#available_quantity_ajax_error_msg').show();
		$('#available_quantity_ajax_msg').hide();
		$('#available_quantity_ajax_success_msg').hide();
	};

	var showAjaxSuccess = function(msg)
	{
		$('#available_quantity_ajax_success_msg').html(msg);
		$('#available_quantity_ajax_error_msg').hide();
		$('#available_quantity_ajax_msg').hide();
		$('#available_quantity_ajax_success_msg').show();
	};

	var showAjaxMsg = function(msg)
	{
		$('#available_quantity_ajax_msg').html(msg);
		$('#available_quantity_ajax_error_msg').hide();
		$('#available_quantity_ajax_msg').show();
		$('#available_quantity_ajax_success_msg').hide();
	};

	var ajaxCall = function(data)
	{
		data.ajaxProductQuantity = 1;
		data.id_product = '{$product->id}';
		data.token = "{$token}";
		data.ajax = 1;
		data.controller = "AdminProducts";
		data.action = "productQuantity";
		showAjaxMsg('{l s='Saving data...'}');
		$.ajax({
			type: "POST",
			url: "ajax-tab.php",
			data: data,
			dataType: 'json',
			async : true,
			success: function(msg)
			{
				if (msg.error)
				{
					showAjaxError('{l s='Error durring saving data'}');
					return;
				}
				showAjaxSuccess('{l s='Data saved'}');
			},
			error: function(msg)
			{
				showAjaxError('{l s='Error durring saving data'}');
			}
		});
	};

	var refreshQtyAvaibilityForm = function()
	{
		if ($('#depends_on_stock_0').attr('checked'))
		{
			$('.available_quantity').find('input').show();
			$('.available_quantity').find('span').hide();
		}
		else
		{
			$('.available_quantity').find('input').hide();
			$('.available_quantity').find('span').show();
		}
	};

	$('.depends_on_stock').click(function(e)
	{
		refreshQtyAvaibilityForm();
		ajaxCall( { actionQty: 'depends_on_stock', value: $(this).val() } );
		if($(this).val() == 0)
			$('.available_quantity input').trigger('change');
	});

	$('.available_quantity').find('input').change(function(e)
	{
		ajaxCall( { actionQty: 'set_qty', id_product_attribute: $(this).parent().attr('id').split('_')[1], value: $(this).val() } );
	});

	$('.available_quantity').find('input').click(function(e)
	{
		if(typeof(this.intervalId) != 'undefined')
			window.clearInterval(this.intervalId);
		this.intervalId = window.setInterval(function(it, initialValue)
		{
			if(initialValue != $(it).val())
			{
				window.clearInterval(it.intervalId);
				$(it).trigger('change');
				$(it).trigger('click');
			}
		}, 500, this, $(this).val())
	});

	$('.out_of_stock').click(function(e)
	{
		refreshQtyAvaibilityForm();
		ajaxCall( { actionQty: 'out_of_stock', value: $(this).val() } );
	});

	refreshQtyAvaibilityForm();
</script>