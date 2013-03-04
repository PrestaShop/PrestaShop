{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script text="javascript">
{literal}
$('document').ready(function(){
	$('#send_friend_button').fancybox({
		'hideOnContentClick': false
	});

	$('#sendEmail').click(function(){
		var datas = [];
		$('#fancybox-content').find('input').each(function(index){
			var o = {};
			o.key = $(this).attr('name');
			o.value = $(this).val();
			if (o.value != '')
				datas.push(o);
		});
		if (datas.length >= 3)
		{
			$.ajax({
				{/literal}url: "{$module_dir}sendtoafriend_ajax.php",{literal}
				post: "POST",
				data: {action: 'sendToMyFriend', secure_key: '{/literal}{$stf_secure_key}{literal}', friend: JSON.stringify(datas)},{/literal}{literal}
				dataType: "json",
				success: function(result){
					$.fancybox.close();
				}
			});
		}
		else
			$('#send_friend_form_error').text("{/literal}{l s='You did not fill required fields' mod='sendtoafriend' js=1}{literal}");
	});
});
{/literal}
</script>
<li class="sendtofriend">
	<a id="send_friend_button" href="#send_friend_form">{l s='Send to a friend' mod='sendtoafriend'}</a>
</li>

<div style="display: none;">
	<div id="send_friend_form">
			<h2 class="title">{l s='Send to a friend' mod='sendtoafriend'}</h2>
			<div class="product clearfix">
				<img src="{$link->getImageLink($stf_product->link_rewrite, $stf_product_cover, 'home_default')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$stf_product->name|escape:html:'UTF-8'}" />
				<div class="product_desc">
					<p class="product_name"><strong>{$stf_product->name}</strong></p>
					{$stf_product->description_short}
				</div>
			</div>
			
			<div class="send_friend_form_content">
				<div id="send_friend_form_error"></div>
				<div class="form_container">
					<p class="intro_form">{l s='Recipient' mod='sendtoafriend'} :</p>
					<p class="text">
						<label for="friend_name">{l s='Name of your friend' mod='sendtoafriend'} <sup class="required">*</sup> :</label>
						<input id="friend_name" name="friend_name" type="text" value=""/>
					</p>
					<p class="text">
						<label for="friend_email">{l s='E-mail address of your friend' mod='sendtoafriend'} <sup class="required">*</sup> :</label>
						<input id="friend_email" name="friend_email" type="text" value=""/>
					</p>
					<p class="txt_required"><sup class="required">*</sup> {l s='Required fields' mod='sendtoafriend'}</p>
				</div>
				<p class="submit">
					<input id="id_product_comment_send" name="id_product" type="hidden" value="{$stf_product->id}" />
					<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='sendtoafriend'}</a>&nbsp;{l s='or' mod='sendtoafriend'}&nbsp;
					<input id="sendEmail" class="button" name="sendEmail" type="submit" value="{l s='Send' mod='sendtoafriend'}" />
				</p>
			</div>
	</div>
</div>
