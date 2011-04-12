<form action="{$formAction}" method="post">
	<input type="hidden" name="method" value="technical_options"/>
	<fieldset>

				<h4 class="clear">{l s='List of triggering status' mod='dejala'}:</h4>
				<div class="margin-form">
					{*<input size="30" type="text" name="triger_status" value="{$trigerringStatuses}" />*}
					<input type='hidden' name='status_max' value='{$statuses|@count}'/>
					{foreach from=$statuses item=status name=statusLoop}
						{assign var='curIdxStatus' value=$smarty.foreach.statusLoop.index}
						<div style="float:left;height:30px;width:50%;">
							<input type='checkbox' name='status_{$curIdxStatus}' value='{$status.id}' {if ('1' == $status.checked)}checked='checked'{/if}/> {$status.label}
						</div>
					{/foreach}

					<p class="clear">{l s='List of statuses that trigger dejala.fr' mod='dejala'}</p>
				</div>
				<div class="margin-form"><input type="submit" name="btnSubmit" value="{l s='Update settings' mod='dejala'}" class="button" /></div>
			</fieldset>
</form>
