 {*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{literal}
<script type="text/javascript">
	/*$(function(){ $('input[@type=radio].star').rating(); });
	$(function(){
		$('.auto-submit-star').rating({
			callback: function(value, link){
			}
		});
	});*/

	$(function(){
		$('input[@type=radio].star').rating();
		$('.auto-submit-star').rating({
			callback: function(value, link) {}
		});
	});

	//close  comment form
	function closeCommentForm(){
		$('#sendComment').slideUp('fast');
		$('input#addCommentButton').fadeIn('slow');
	}

	$('document').ready(function(){

		var limitInputText = {
				'maxCharacterSize': 200,
				'originalStyle': 'originalDisplayInfo',
				'warningStyle': 'warningDisplayInfo',
				'warningNumber': 40,
				'displayFormat': '#left'
			};

		$('#commentContent').textareaCount(limitInputText);

		$('#new_comment_btn').fancybox({
			'hideOnContentClick': false,
			'onClosed': function(){
			},
		});

		$('a[href=#idTab5]').click(function(){
			$('*[id^="idTab"]').addClass('block_hidden_only_for_screen');
			$('div#idTab5').removeClass('block_hidden_only_for_screen');

			$('ul#more_info_tabs a[href^="#idTab"]').removeClass('selected');
			$('a[href="#idTab5"]').addClass('selected');
		});

		$('#submitMessage').click(function(){
			var datas = [];
			$('#fancybox-content').find('input, textarea, select').each(function(index){
				var o = {}
				o.key = $(this).attr('name');
				o.value = $(this).val();
				datas.push(o);
			});
			$.ajax({
				url: "{/literal}{$module_dir}{literal}productcomments-ajax.php",
				post: "POST",
				data: {action: 'sendComment', secure_key: '{/literal}{$secure_key}{literal}', review: JSON.stringify(datas)},
				dataType: "json",
				success: function(result){
                    if (result == 0) {
                        alert('{/literal}{l s='Your comment can not be posted. Please fill in all required fields.' mod='productcomments'}{literal}');
                    } else {
                        location.reload( true );
                        $.fancybox.close();
                    }
	 		 	}
			});
		});

		$('#submitNewMessage').click(function(){
			var datas = [];
			$('#fancybox-content').find('input, textarea, select').each(function(index){
				var o = {}
				o.key = $(this).attr('name');
				o.value = $(this).val();
				datas.push(o);
			});
			$.ajax({
				url: "{/literal}{$module_dir}{literal}productcomments-ajax.php",
				post: "POST",
				data: {action: 'sendComment', secure_key: '{/literal}{$secure_key}{literal}', review: JSON.stringify(datas)},
				dataType: "json",
				success: function(result){
                    if (result == 0) {
                        alert('{/literal}{l s='Your comment can not be posted. Please fill in all required fields.' mod='productcomments'}{literal}');
                    } else {
                        location.reload( true );
                        $.fancybox.close();
                    }
	 		 	}
			});
		});
	});
</script>
{/literal}

<div id="product_comments_block_extra">
	{if $nbComments != 0}
	<div class="comments_note">
		<span>{l s='Average grade' mod='productcomments'}&nbsp</span>
		<div class="star_content clearfix">
		{section name="i" start=0 loop=5 step=1}
			{if $averageTotal le $smarty.section.i.index}
				<div class="star"></div>
			{else}
				<div class="star star_on"></div>
			{/if}
		{/section}
		</div>
	</div>
	{/if}

	<div class="comments_advices">
		{if $nbComments != 0}
		<a href="#idTab5">{l s='Read user reviews' mod='productcomments'} ({$nbComments})</a><br/>
		{/if}
		{if ($too_early == false AND ($logged OR $allow_guests))}
		<a id="new_comment_btn" href="#new_comment_form">{l s='Write your review' mod='productcomments'}</a>
		{/if}
	</div>
	<div style="display: none;">
		<div id="new_comment_form">
			<h2 class="title">{l s='Write your review' mod='productcomments'}</h2>
			<div class="product clearfix">
				<img src="{$link->getImageLink($product->link_rewrite, $productcomment_cover, 'home')}" height="{$homeSize.height}" width="{$homeSize.width}" alt="{$product->name|escape:html:'UTF-8'}" />
				<div class="product_desc">
					<p class="product_name"><strong>{$product->name}</strong></p>
					{$product->description_short}
				</div>
			</div>

			<div class="new_comment_form_content">
				<p class="intro_form">{l s='Write your review' mod='productcomments'}</p>
				{if $criterions|@count > 0}
					<ul class="grade_content clearfix">
					{section loop=$criterions name=i start=0 step=1}
						<li class="clearfix">
							<span>
								<input type="hidden" name="id_product_comment_criterion_{$smarty.section.i.iteration}" value="{$criterions[i].id_product_comment_criterion|intval}" />
								{$criterions[i].name|escape:'html':'UTF-8'}:&nbsp;
							</span>
							<div class="star_content">
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" id="{$smarty.section.i.iteration}_grade" value="1" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="2" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="3" checked="checked" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="4" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="5" />
							</div>
						</li>
					{/section}
					</ul>
				{/if}
				<div class="form_contenair">
					<p class="text">
						<label for="comment_title">{l s='Title' mod='productcomments'} <sup>*</sup>:</label>
						<input id="commentTitle" name="title" type="text" value=""/>
					</p>
					<p class="textarea">
						<label for="content">{l s='Comment' mod='productcomments'} <sup>*</sup>:</label>
						<textarea id="commentContent" name="content"></textarea>
					</p>
					{if $allow_guests == true && $logged == 0}
					<p class="text">
						<label>{l s='Your name' mod='productcomments'} <sup>*</sup>:</label>
						<input id="commentCustomerName" name="customer_name" type="text" value=""/>
					</p>
					{/if}
					<p class="submit">
						<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}'></input>
						<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='productcomments'}</a>&nbsp;
						{l s='or' mod='productcomments'}&nbsp;&nbsp;<button id="submitMessage" name="submitMessage" type="submit">{l s='Send' mod='productcomments'}</button>
					</p>
					<p class="txt_required">* {l s='Required fields' mod='productcomments'}</p>
				</div>
			</div><!-- /end new_comment_form_content -->
		</div>
	</div>
</div>
<!--  /Module ProductComments -->