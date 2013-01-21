<!-- MODULE Block contact infos -->
<div id="block_contact_infos">
	<h4>{l s='Contact us' mod='blockcontactinfos'}</h4>
	<ul>
		{if $blockcontactinfos_company != ''}<li><strong>{$blockcontactinfos_company|escape:'htmlall':'UTF-8'}</strong></li>{/if}
		{if $blockcontactinfos_address != ''}<li>{$blockcontactinfos_address}</li>{/if}
		{if $blockcontactinfos_phone != ''}<li class="tel"> {$blockcontactinfos_phone}</li>{/if}
		{if $blockcontactinfos_email != ''}<li class="tel"> {mailto address=$blockcontactinfos_email encode="hex"}</li>{/if}
	</ul>
</div>
<!-- /MODULE Block contact infos -->
