{*
* 2007-2013 PrestaShop
**
* NOTICE OF LICENSE
**
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
**
* DISCLAIMER
**
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
**
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="leadin">{block name="leadin"}{/block}</div>
{if $module_confirmation}
<div class="module_confirmation conf confirm">
	{l s='Your .CSV file has been sucessfully imported into your shop. Don\'t forget to Re-build the products search index.'}
</div>
{/if}

<div class="row">
	<div class="col-lg-8">
		{* Import fieldset *}
		<div class="panel">
			<h3>
				<i class="icon-upload"></i>
				{l s='Import'}
			</h3>
			<div class="alert alert-info">		
				<ul class="list-unstyled">
					<li>{l s='You can read information on CSV import at:'}
						<a href="http://doc.prestashop.com/display/PS16/CSV+Import+Parameters" target="_blank">http://doc.prestashop.com/display/PS16/CSV+Import+Parameters</a>
					</li>
					<li>{l s='Read more about CSV format at:'}
						<a href="http://en.wikipedia.org/wiki/Comma-separated_values" target="_blank">http://en.wikipedia.org/wiki/Comma-separated_values</a>
					</li>
				</ul>
			</div>
			<hr>
			<!-- <div id="upload_file_import">
				<form action="{$current}&token={$token}" method="post" enctype="multipart/form-data" class="form-horizontal">
					<button type="submit" name="submitFileUpload" class="btn btn-default">
						<i class="icon-upload-alt"></i>
						{l s='Upload'}
					</button>
				</form>
			</div> -->
			<form id="preview_import" action="{$current}&token={$token}" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-lg-4">{l s='What kind of entity would you like to import?'} </label>
					<div class="col-lg-8">
						<select name="entity" id="entity" class="fixed-width-xl">
							{foreach $entities AS $entity => $i }
							<option value="{$i}"{if $entity_selected == $i} selected="selected"{/if}>
								{$entity}
							</option>
							{/foreach}
						</select>
					</div>
				</div>

				<div class="alert alert-warning import_products_categories">
					<ul>
						<li>{l s='Note that the category import does not support categories of the same name.'}</li>
						<li>{l s='Note that you can have several products with the same reference.'}</li>
					</ul>
				</div>

				<div class="alert alert-warning import_supply_orders_details">
					<p>{l s='Importing Supply Order Details will reset products ordered, if there are any.'}</p>
				</div>

				<hr>
				<!--<div class="form-group" id="csv_file_uploader">
					<label class="control-label col-lg-4">{l s='Select your CSV file'}</label>
					<div class="col-lg-8">
						<input id="file" type="file" name="file" class="hide" />
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="file-name" name="filename" type="text" class="disabled"  readonly />
							<span class="input-group-btn">
								<button id="file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> {l s='Choose a file'}
								</button>
							</span>
						</div>
						<p class="help-block">
							{l s="You can also re-use a file that you already uploaded."}
							<a href="#" onclick="$('#csv_files_history').slideToggle();$('#csv_file_uploader').slideToggle(); return false;">Click here</a>.
						</p>
					</div>
					 <div class="alert alert-info">
						{l s='Only UTF-8 and ISO-8859-1 encoding are allowed'}.<br/>
						{l s='You can also upload your file via FTP to the following directory:'} {$path_import}.
					</div> 
				</div>-->

				<div class="form-group" id="csv_file_uploader">
					<label class="control-label col-lg-4">{l s='Select your CSV file'}</label>
					<div class="col-lg-8">
						<input id="file" type="file" name="file" data-url="{$current}&token={$token}&ajax=1&action=uploadCsv" class="hide" />
						<button class="ladda-button btn btn-default" data-style="expand-right" data-size="s" type="button" id="file-add-button">
							<i class="icon-folder-open"></i>
							{l s='Add file'}
						</button>
						{l s='or'}
						<button class="btn btn-default" type="button" onclick="$('#csv_files_history').slideToggle();$('#csv_file_uploader').slideToggle(); return false;">
							<span class="csv-history-nb badge">{$files_to_import|count}</span>
							{l s="Choose from history / FTP"}
						</button>
						<p class="help-block">
							{l s='Only UTF-8 and ISO-8859-1 encoding are allowed'}.<br/>
							{l s='You can also upload your file via FTP to the following directory:'} {$path_import}.
						</p>
					</div>
					<div class="alert alert-danger" id="file-errors" style="display:none"></div>
				</div>

				<script type="text/javascript">
					function humanizeSize(bytes) {
						if (typeof bytes !== 'number')
							return '';
						if (bytes >= 1000000000)
							return (bytes / 1000000000).toFixed(2) + ' GB';
						if (bytes >= 1000000)
							return (bytes / 1000000).toFixed(2) + ' MB';
						return (bytes / 1000).toFixed(2) + ' KB';
					}

					function csv_select(filename) {
						$('#csv_selected_value').val(filename);
						$('#csv_selected_filename').html(filename);
						$('#csv_file_selected').show();
						$('#csv_file_uploader').hide();
						$('#csv_files_history').hide();
					}
					
					function csv_unselect() {
						$('#csv_file_selected').hide();
						$('#csv_file_uploader').show();
					}

					function init_selected() {
						$('#csv_file_selected').show();
						$('#csv_file_uploader').hide();
					}

					$(document).ready(function() {

						var file_add_button = Ladda.create( document.querySelector('#file-add-button' ));
						var file_total_files = 0;

						$('#file').fileupload({
							dataType: 'json',
							autoUpload: true,
							acceptFileTypes: /(\.|\/)(csv)$/i,
							singleFileUploads: true,
							{if isset ($post_max_size)}maxFileSize: {$post_max_size},{/if}
							start: function (e) {
								file_add_button.start();
							},
							fail: function (e, data) {
								$('#file-errors').html(data.errorThrown.message).show();
							},
							done: function (e, data) {
								if (data.result) {
									if (typeof data.result.file !== 'undefined') {
										if (typeof data.result.file.error !== 'undefined' && data.result.file.error != '')
											$('#file-errors').html('<strong>'+data.result.file.name+'</strong> : '+data.result.file.error).show();
										else {
											//$('#file-success').show();
											$(data.context).find('button').remove();
											$('#csv_uploaded_history').append($('#csv_uploaded_history tr:first').clone());
											$('#csv_uploaded_history tr:last td:first').html(data.result.file.filename);
											$('#csv_uploaded_history tr:last button').data('filename', data.result.file.filename);
											csv_select(data.result.file.filename);
										}
									}
								}
							},
						}).on('fileuploadalways', function (e, data) {
							file_add_button.stop();
						}).on('fileuploadprocessalways', function (e, data) {
							var index = data.index,	file = data.files[index];
							
							if (file.error) {
								$('#file-errors').append('<strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error).show();
								$(data.context).find('button').trigger('click');
							}
						});
						$('#csv_uploaded_history').on('click', 'button', function(e){
							e.preventDefault();
							var filename = $(this).data('filename');
							csv_select(filename);
						});
						$('#file-add-button').on('click', function(e) {
							e.preventDefault();
							$('#file-success').hide();
							$('#file-errors').html('').hide();
							$('#file').trigger('click');
						});
						$('#file-remove-button').on('click', function(e) {
							e.preventDefault();
							csv_unselect();
						});

						var selected = '{$csv_selected}';
						console.log(selected);
						if(selected){
							init_selected();
						}
					});
				</script>
				<!-- <div class="form-group">
					<label class="control-label col-lg-3">
						{if count($files_to_import) > 1}
							{l s='Your CSV file (%d files):' sprintf=count($files_to_import)}
						{else}
							{l s='Your CSV file (%d file):' sprintf=count($files_to_import)}
						{/if}
					</label>
					<div class="col-lg-9">
					{if count($files_to_import)}
						<select name="csv">
							{foreach $files_to_import AS $filename}
								<option value="{$filename}"{if $csv_selected == $filename} selected="selected"{/if}>{$filename|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
					{/if}
					</div>
				</div> -->
				<div class="form-group" id="csv_files_history" style="display:none;" >
					<div class="panel">
						<div class="panel-heading">
							<!-- {l s='Click to view your csv files.'} -->
							{l s='History of uploaded .CSV'}
							<span class="csv-history-nb badge">{$files_to_import|count}</span>
							<button type="button" class="btn btn-link pull-right" onclick="$('#csv_files_history').toggle();$('#csv_file_uploader').toggle(); return false;">
								<i class="icon-remove"></i>
							</button>
						</div>
						<table id="csv_uploaded_history" class="table">
							{foreach $files_to_import AS $filename}
							<tr >
								<td>
									{$filename}
								</td>
								<td>
									<div class="btn-group pull-right">
										<button type="button" data-filename="{$filename|escape:'html':'UTF-8'}" class="btn btn-default">
											<i class="icon-ok"></i>
											{l s='Use'}
										</button>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<i class="icon-chevron-down"></i>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a href="{$current}&token={$token}&csvfilename={$filename|@base64_encode}" target="_blank">
													<i class="icon-download"></i>
													{l s='Download'}
												</a>
											</li>
											<li class="divider"></li>
											<li>
												<a href="{$current}&token={$token}&csvfilename={$filename|@base64_encode}&delete=1">
													<i class="icon-trash"></i>
													{l s='Delete'}
												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							{/foreach}
						</table>
					</div>
				</div>
				<div class="form-group" id="csv_file_selected" style="display: none;">
					<div class="alert alert-success clearfix">
						<input type="hidden" value="{$filename}" name="csv" id="csv_selected_value">
						<div class="col-lg-8">
							<span id="csv_selected_filename">{$csv_selected|escape:'html':'UTF-8'}</span>
						</div>
						<div class="col-lg-4">
							<div class="btn-group pull-right">
								<button id="file-remove-button" type="button" class="btn btn-default">
									<i class="icon-refresh"></i>
									{l s='Change'}
								</button>
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<label class="control-label col-lg-4">
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='The locale must be installed'}">
							{l s='Language of the file'}
						</span>
					</label>
					<div class="col-lg-8">
						<select name="iso_lang" class="fixed-width-xl">
							{foreach $languages AS $lang}
								<option value="{$lang.iso_code}" {if $lang.id_lang == $id_language} selected="selected"{/if}>{$lang.name}</option>
							{/foreach}
						</select>
					</div>
				</div>			
				<div class="form-group">
					<label for="convert" class="control-label col-lg-4">{l s='ISO-8859-1 encoded file?'} </label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="convert" id="convert" type="checkbox" />
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn btn-default"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">{l s='Field separator'} </label>
					<div class="col-lg-8">
						<input class="fixed-width-xs" type="text" value="{if isset($separator_selected)}{$separator_selected|escape:'html':'UTF-8'}{else};{/if}" name="separator"/>
						<div class="help-block">{l s='e.g. '} 1; Ipod; 129.90; 5</div>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-4">{l s='Multiple value separator'} </label>
					<div class="col-lg-8">
						<input class="fixed-width-xs" type="text" value="{if isset($multiple_value_separator_selected)}{$multiple_value_separator_selected|escape:'html':'UTF-8'}{else},{/if}" name="multiple_value_separator"/>
						<div class="help-block">{l s='e.g. '} Ipod; red.jpg, blue.jpg, green.jpg; 129.90</div>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<label for="truncate" class="control-label col-lg-4">{l s='Delete all'} <span id="entitie">{l s='categories'}</span> {l s='before import?'} </label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="truncate" id="truncate" type="checkbox"/>
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn btn-default"></a>
						</label>
					</div>
				</div>
				<div class="form-group" style="display: none">
					<label for="match_ref" class="control-label col-lg-4">{l s='Use product reference as key?'}</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="match_ref" id="match_ref" type="checkbox" />
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn btn-default"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="regenerate" class="control-label col-lg-4">{l s='No thumbnails regeneration'}</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="regenerate" id="regenerate" type="checkbox" />
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn btn-default"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="forceIDs" class="control-label col-lg-4">
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='If you don\'t use this option, all ID\'s will be auto-incremented.'}">
							{l s='Force all ID'} 
						</span>
					</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="forceIDs" id="forceIDs" type="checkbox"/>
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn btn-default"></a>
						</label>
					</div>
				</div>
<!-- 				<hr>
				<div class="form-group">
					<div class="col-lg-9 col-lg-push-3">
						<button type="submit" name="submitImportFile" id="submitImportFile" class="btn btn-default pull-right" {if empty($files_to_import)}disabled{/if}>
							{l s='Next step'}
							<i class="icon-circle-arrow-right"></i>
						</button>
					</div>
				</div> -->

				<!-- {if empty($files_to_import)}
				<div class="alert alert-info">{l s='You must upload a file in order to proceed to the next step'}</div>
				{/if} -->

				<!--{if !count($files_to_import)}
				 <p>{l s='There is no CSV file available. Please upload one using the \'Upload\' button above.'}</p> 
				{/if}-->
				<div class="panel-footer">
					<button type="submit" name="submitImportFile" id="submitImportFile" class="btn btn-default pull-right">
						<i class="process-icon-next"></i> <span>{l s='Next step'}</span>
					</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-lg-4">
		{* Available and required fields *}
		<div class="panel">
			<h3>
				<i class="icon-list-alt"></i>
				{l s='Available fields'}
			</h3>
			<div id="availableFields" class="alert alert-warning">
				{$available_fields}
			</div>
			<p>{l s='* Required field'}</p>
		</div>
		<div class="panel">
			<a href="#" onclick="$('#sample_files_import').slideToggle(); return false;">
				{l s='Click to view our sample import csv files.'}
			</a>

			<div id="sample_files_import" class="list-group" style="display:none">
				<a class="list-group-item" href="../docs/csv_import/categories_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Categories file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/products_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Products file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/combinations_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Combinations file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/customers_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Customers file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/addresses_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Addresses file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/manufacturers_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Manufacturers file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/suppliers_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Suppliers file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/alias_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Sample Alias file'}
				</a>
				{if $PS_ADVANCED_STOCK_MANAGEMENT}
				<a class="list-group-item" href="../docs/csv_import/supply_orders_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Supply Orders sample file'}
				</a>
				<a class="list-group-item" href="../docs/csv_import/supply_orders_details_import.csv" target="_blank">
					<i class="icon-download"></i>
					{l s='Supply Orders Details sample file'}
				</a>
				{/if}
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var truncateAuthorized = {$truncateAuthorized|intval};
		activeClueTip();
		
		$('#preview_import').submit(function(e){
			if ($('#truncate').get(0).checked)
				if (truncateAuthorized)
				{
					if (!confirm('{l s='Are you sure that you would like to delete this' js=1}' + ' ' + $.trim($('#entity > option:selected').text().toLowerCase()) + '?'))
						e.preventDefault();
				}
				else
				{
					jAlert('{l s='You do not have permission to delete here. When the multistore is enabled, only a SuperAdmin can delete all items before an import.' js=1}');
					return false;
				}
		});

		$("select#entity").change(function(){
			if ($("#entity > option:selected").val() == 8 || $("#entity > option:selected").val() == 9)
				$("label[for=truncate],#truncate").hide();
			else
				$("label[for=truncate],#truncate").show();
	
			if ($("#entity > option:selected").val() == 9)
				$(".import_supply_orders_details").show();
			else
			{
				$(".import_supply_orders_details").hide();
				$('input[name=multiple_value_separator]').val('{if isset($multiple_value_separator_selected)}{$multiple_value_separator_selected}{else},{/if}');
			}
			if ($("#entity > option:selected").val() == 1)
				$("#match_ref").closest('.form-group.').show();
			else
				$("#match_ref").closest('.form-group.').hide();

			if ($("#entity > option:selected").val() == 1 || $("#entity > option:selected").val() == 0)
				$(".import_products_categories").show();
			else
				$(".import_products_categories").hide();

			if ($("#entity > option:selected").val() == 0 || $("#entity > option:selected").val() == 1 || 
				$("#entity > option:selected").val() == 5 || $("#entity > option:selected").val() == 6)
				$("#regenerate").closest('.form-group.').show();
			else
				$("#regenerate").closest('.form-group.').hide();

			if ($("#entity > option:selected").val() == 0 || $("#entity > option:selected").val() == 1 || $("#entity > option:selected").val() == 3 || $("#entity > option:selected").val() == 5 || $("#entity > option:selected").val() == 6 || $("#entity > option:selected").val() == 7)
				$("#forceIDs").closest('.form-group.').show();
			else
				$("#forceIDs").closest('.form-group.').hide();
			$("#entitie").html($("#entity > option:selected").text().toLowerCase());
			$.ajax({
				url: 'ajax.php',
				data: {
					getAvailableFields:1,
					entity: $("#entity").val()
				},
				dataType: 'json',
				success: function(j){
					var fields = "";
					$("#availableFields").empty();
					
					for (var i = 0; i < j.length; i++)
						fields += j[i].field;
	
					$("#availableFields").html(fields);
					activeClueTip();
				},
				error: function(j){}			
			});
		});
		$("select#entity").trigger('change');
		function activeClueTip()
		{
			$('.info_import').cluetip({
				splitTitle: '|',
			    showTitle: false
		 	});
		};	

		$('#file-selectbutton').click(function(e){
			$('#file').trigger('click');
		});
		$('#filename').click(function(e){
			$('#file').trigger('click');
		});
		$('#file').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#filename').val(file[file.length-1]);
		});
	});
</script>
