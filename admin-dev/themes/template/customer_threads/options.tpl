{extends file="helper/options/options.tpl"}
{block name="after"}
{if $use_sync}
		<fieldset><legend>{l s='Sync'}</legend>
			<label>{l s='Run sync:'}</label>
			<div class="margin-form">
				<button class="button" id="run_sync" onclick="run_sync();">{l s='Run sync'}</button>
				<p>{l s='Click to synchronize mail automatically'}</p>
				<div id="ajax_loader"></div>
				<div class="error" style="display:none" id="ajax_error"></div>
				<div class="conf" style="display:none" id="ajax_conf"></div>
			</div>
		</fieldset><br/>
		<script type="text/javascript"> 
			var ajaxQueries = new Array();
			function run_sync () {
				$('#ajax_error').html('');
				$('#ajax_error').hide();
				$('#ajax_conf').html('');
				$('#ajax_conf').hide();
				for(i = 0; i < ajaxQueries.length; i++)
					ajaxQueries[i].abort();
				ajaxQueries = new Array();
				$('#ajax_loader').html('<img src="{$smarty.const._PS_ADMIN_IMG_}ajax-loader.gif">');
				ajaxQuery = $.ajax({
					type: "POST",
					url: "ajax.php",
					data: "syncImapMail=1",
					dataType : "json",
					success: function(jsonData) {
						jsonError = '';
						if (jsonData.hasError)
						{
							for (i=0;i < jsonData.errors.length;i++)
								jsonError = jsonError+'<li>'+jsonData.errors[i]+'</li>';
							$('#ajax_error').html('<ul>'+jsonError+'</ul>');
							$('#ajax_error').fadeIn();
						}
						else
						{
							jsonError = '<li>{l s='Sync success'}</li>';
							for (i=0;i < jsonData.errors.length;i++)
								jsonError = jsonError+'<li>'+jsonData.errors[i]+'</li>';
							$('#ajax_conf').html('<ul>'+jsonError+'</ul>');
							$('#ajax_conf').fadeIn();
						}
							
						$('#ajax_loader').html('');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("TECHNICAL ERROR: unable to sync.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
				ajaxQueries.push(ajaxQuery);
				
			};
		</script>
{/if}
{/block}
