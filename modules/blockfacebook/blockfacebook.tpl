{if $facebookurl != ''}
	<div id="facebook_block" class="col-xs-4">
		<h4 >{l s='Follow us on facebook' mod='blockfacebook'}</h4>
            <div class="facebook-fanbox">
           		 <iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2F{$facebookurl|escape:'html':'UTF-8'}&amp;width=235&amp;height=290&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false" scrolling="no" frameborder="0" style="border:none;  width:100%; height:200px;" allowTransparency="true"></iframe>
            </div>
	</div>
{/if}


