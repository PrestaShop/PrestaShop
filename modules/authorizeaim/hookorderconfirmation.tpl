{if $status == 'ok'}
	<p>{l s='Your order on' mod='authorizeaim'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='authorizeaim'}
		<br /><br /><span class="bold">{l s='Your order will be sent as soon as possible.' mod='authorizeaim'}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='authorizeaim'} <a href="{$base_dir_ssl}contact-form.php">{l s='customer support' mod='authorizeaim'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='authorizeaim'} 
		<a href="{$base_dir_ssl}contact-form.php">{l s='customer support' mod='authorizeaim'}</a>.
	</p>
{/if}