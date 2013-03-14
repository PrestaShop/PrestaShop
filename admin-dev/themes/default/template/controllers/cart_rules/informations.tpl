<table cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<label>{l s='Name'}</label>
			<div class="margin-form">
				<div class="translatable">
				{foreach from=$languages item=language}
					<div class="lang_{$language.id_lang|intval}" style="display:{if $language.id_lang == $id_lang_default}block{else}none{/if};float:left">
						<input type="text" id="name_{$language.id_lang|intval}" name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:html:'UTF-8'}" style="width:400px" />
						<sup>*</sup>
					</div>
				{/foreach}
				</div>
				<p class="preference_description">{l s='This will be displayed in the cart summary, as well as on the invoice.'}</p>
			</div>
			<label>{l s='Description'}</label>
			<div class="margin-form">
				<textarea name="description" style="width:80%;height:100px">{$currentTab->getFieldValue($currentObject, 'description')|escape}</textarea>
				<p class="preference_description">{l s='For your eyes only. This will never be displayed to the customer.'}</p>
			</div>
			<label>{l s='Code'}</label>
			<div class="margin-form">
				<input type="text" id="code" name="code" value="{$currentTab->getFieldValue($currentObject, 'code')|escape}" />
				<a href="javascript:gencode(8);" class="button">{l s='(Click to generate random code)'}</a>
				<p class="preference_description">{l s='Caution! The rule will automatically be applied if you leave this field blank.'}</p>
			</div>
			<label>{l s='Highlight'}</label>
			<div class="margin-form">
				&nbsp;&nbsp;
				<input type="radio" name="highlight" id="highlight_on" value="1" {if $currentTab->getFieldValue($currentObject, 'highlight')|intval}checked="checked"{/if} />
				<label class="t" for="highlight_on"> <img src="../img/admin/enabled.gif" alt="{l s='Yes'}" title="{l s='Yes'}" style="cursor:pointer" /></label>
				&nbsp;&nbsp;
				<input type="radio" name="highlight" id="highlight_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'highlight')|intval}checked="checked"{/if} />
				<label class="t" for="highlight_off"> <img src="../img/admin/disabled.gif" alt="{l s='No'}" title="{l s='No'}" style="cursor:pointer" /></label>
				<p class="preference_description">
					{l s='If the voucher is not yet in the cart, it will be displayed in the cart summary.'}
				</p>
			</div>
			<label>{l s='Partial use'}</label>
			<div class="margin-form">
				&nbsp;&nbsp;
				<input type="radio" name="partial_use" id="partial_use_on" value="1" {if $currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
				<label class="t" for="partial_use_on"> <img src="../img/admin/enabled.gif" alt="{l s='Allowed'}" title="{l s='Allowed'}" style="cursor:pointer" /></label>
				&nbsp;&nbsp;
				<input type="radio" name="partial_use" id="partial_use_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'partial_use')|intval}checked="checked"{/if} />
				<label class="t" for="partial_use_off"> <img src="../img/admin/disabled.gif" alt="{l s='Not allowed'}" title="{l s='Not allowed'}" style="cursor:pointer" /></label>
				<p class="preference_description">
					{l s='Only applicable if the voucher value is greater than the cart total.'}<br />
					{l s='If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.'}
				</p>
			</div>
			<label>{l s='Priority'}</label>
			<div class="margin-form">
				<input type="text" name="priority" value="{$currentTab->getFieldValue($currentObject, 'priority')|intval}" />
				<p class="preference_description">{l s='Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".'}</p>
			</div>
			<label>{l s='Status'}</label>
			<div class="margin-form">
				&nbsp;&nbsp;
				<input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
				<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled'}" title="{l s='Enabled'}" style="cursor:pointer" /></label>
				&nbsp;&nbsp;
				<input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if} />
				<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled'}" title="{l s='Disabled'}" style="cursor:pointer" /></label>
			</div>
		</td>
	</tr>
</table>