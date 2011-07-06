<p class="payment_module">
	<a href="javascript:document.dibs_form.submit();" title="{l s='Pay with dibs' mod='dibs'}" style="height:48px">
		<span style="height:40px;width:86px;float:left"><img src="{$module_dir}logos/dibs_{$logo_color}.jpg" alt="{l s='dibs logo' mod='dibs'}" /></span>
		<span style="width:350px;float:left;margin-left:10px"><strong>{l s='Pay with dibs' mod='dibs'}</strong><br />{l s='Pay safely and quickly with dibs.' mod='dibs'}</span>
		<div style="clear:both;height:0;line-height:0">&nbsp;</div>
	</a>
	<div style="clear:both;height:0;line-height:0">&nbsp;</div>
</p>
<form name="dibs_form" action="https://payment.architrade.com/paymentweb/start.action" method="post">
{foreach from=$p key=k item=v}
	<input type="hidden" name="{$k}"  value="{$v}" />
{/foreach}
</form>