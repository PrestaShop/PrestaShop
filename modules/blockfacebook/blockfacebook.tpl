{if $facebookurl != ''}
<div id="fb-root"></div>
<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=334341610034299";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<div id="facebook_block" class="col-xs-4">
	<h4 >{l s='Follow us on facebook' mod='blockfacebook'}</h4>
	<div class="facebook-fanbox">
		<div
			class="fb-like-box"
			data-href="http://www.facebook.com/{$facebookurl|escape:'html':'UTF-8'}"
			data-colorscheme="light"
			data-show-faces="true"
			data-header="false"
			data-stream="false"
			data-show-border="false">
		</div>
	</div>
</div>
{/if}