{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{extends file="helpers/options/options.tpl"}
{block name="after"}
{if $use_sync}
		<div class="panel">
			<legend>{l s='Sync' d='Admin.Orderscustomers.Feature'}</legend>
			<label>{l s='Run sync:' d='Admin.Orderscustomers.Feature'}</label>
			<div class="margin-form">
				<button class="btn" id="run_sync" onclick="run_sync();">{l s='Run sync' d='Admin.Orderscustomers.Feature'}</button>
				<p>{l s='Click to synchronize mail automatically' d='Admin.Orderscustomers.Feature'}</p>
				<div id="ajax_loader"></div>
				<div class="error" style="display:none" id="ajax_error"></div>
				<div class="alert" style="display:none" id="ajax_conf"></div>
			</div>
		</div>

		<script type="text/javascript">
			var ajaxQueries = new Array();
			function run_sync()
			{
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
					url: "index.php",
					data: {
						ajax: "1",
						token: "{$token|escape:'html':'UTF-8'}",
						syncImapMail: "1",
						ajax:"1",
						action:"syncImap",
						tab:"AdminCustomerThreads"
					},
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
					error: function(XMLHttpRequest, textStatus, errorThrown)
					{
						jAlert("TECHNICAL ERROR: unable to sync.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
				ajaxQueries.push(ajaxQuery);

			};
		</script>
{/if}
{/block}
