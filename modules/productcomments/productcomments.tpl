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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript" src="{$module_dir}js/jquery.rating.pack.js"></script>
<script type="text/javascript">
	$(function(){literal}{{/literal} $('input[@type=radio].star').rating(); {literal}}{/literal});
	$(function(){literal}{{/literal}
		$('.auto-submit-star').rating({literal}{{/literal}
			callback: function(value, link){literal}{{/literal}
			{literal}}{/literal}
		{literal}}{/literal});
	{literal}}{/literal});

{literal}
	$('document').ready(function(){

		$('#new_comment_tab_btn').fancybox({
			'hideOnContentClick': false,
			'onClosed': function(){
			},
		});
		
		$('button[id^=comment_useful_yes_]').click(function(){

			var idProductComment = $(this).attr('id').replace('comment_useful_yes_', '');
			var parent = $(this).parent();
			
			$.ajax({
				{/literal}url: "{$module_dir}productcomments-ajax.php",{literal}
				post: "POST",
				data: "id_product_comment=" + idProductComment + "&action=usefulness&value=1",
				success: function(result){
					parent.fadeOut("normal", function() {
						parent.remove();
					});
	 		 	}
			});
		});

		$('button[id^=comment_useful_no_]').click(function(){

			var idProductComment = $(this).attr('id').replace('comment_useful_no_', '');
			var parent = $(this).parent();
			
			$.ajax({
				{/literal}url: "{$module_dir}productcomments-ajax.php",{literal}
				post: "POST",
				data: "id_product_comment=" + idProductComment + "&action=usefulness&value=0",
				success: function(result){
					parent.fadeOut("normal", function() {
						parent.remove();
					});
	 		 	}
			});
		});

		$('span[id^=comment_report_]').click(function(){

			{/literal}if (confirm('{l s='Are you sure you want report this comment?' mod='productcomments'}')){literal}
			{
				var idProductComment = $(this).attr('id').replace('comment_report_', '');
				var parent = $(this).parent();
				
				$.ajax({
					{/literal}url: "{$module_dir}productcomments-ajax.php",{literal}
					post: "POST",
					data: "id_product_comment=" + idProductComment + "&action=report",
					success: function(result){
						parent.fadeOut("normal", function() {
							parent.remove();
						});
		 		 	}
				});	
			}
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
				{/literal}url: "{$module_dir}productcomments-ajax.php",{literal}
				post: "POST",
				data: {action: 'sendComment', secure_key: '{/literal}{$secure_key}{literal}', review: JSON.stringify(datas)},
				dataType: "json",
				success: function(result){
					$.fancybox.close();
	 		 	}
			});
		});
	});
{/literal}
</script>

<div id="idTab5">
	<div id="product_comments_block_tab">
	{if $comments}
		{foreach from=$comments item=comment}
			{if $comment.content}
			<div class="comment clearfix">
				<div class="comment_author">
					<span>{l s='Grade' mod='productcomments'}&nbsp</span>
					<div class="star_content clearfix">
					{section name="i" start=0 loop=5 step=1}
						{if $comment.grade le $smarty.section.i.index}
							<div class="star"></div>
						{else}
							<div class="star star_on"></div>
						{/if}
					{/section}
					</div>
					<div class="comment_author_infos">
						<strong>{$comment.customer_name|escape:'html':'UTF-8'}</strong><br/>
						<em>{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</em>
					</div>
				</div>
				<div class="comment_details">
					<h4>{$comment.title}</h4>
					<p>{$comment.content|escape:'html':'UTF-8'|nl2br}</p>
					<ul>
						{if $comment.total_advice > 0}
							<li>{$comment.total_useful} {l s='out of' mod='productcomments'} {$comment.total_advice} {l s='people found this review useful' mod='productcomments'}</li>
						{/if}
						{if $logged == 1}
							{if !$comment.customer_advice}
							<li>{l s='Was this comment useful to you?' mod='productcomments'}<button class="usefulness_btn" id="comment_useful_yes_{$comment.id_product_comment}">{l s='yes' mod='productcomments'}</button><button class="usefulness_btn" id="comment_useful_no_{$comment.id_product_comment}">{l s='no' mod='productcomments'}</button></li>
							{/if}
							{if !$comment.customer_report}
							<li><span class="report_btn" id="comment_report_{$comment.id_product_comment}">{l s='Report abuse' mod='productcomments'}</span></li>
							{/if}
						{/if}
					</ul>
				</div>
			</div>
			{/if}
		{/foreach}
	{else}
		{if ($too_early == false AND ($logged OR $allow_guests))}
		
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
					<div class="grade_content clearfix">
					{section loop=$criterions name=i start=0 step=1}
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
					{/section}
					</div>
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
							<label>{l s='Your name:' mod='productcomments'} <sup>*</sup>:</label>
							<input id="commentCustomerName" name="customer_name" type="text" value=""/>
						</p>
						{/if}
						<p class="submit">
							<span class="txt_required">* {l s='Required fields' mod='productcomments'}</span>
							<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}'></input>
							<button id="submitNewMessage" name="submitMessage" type="submit">{l s='Send' mod='productcomments'}</button>&nbsp;
							{l s='or' mod='productcomments'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='productcomments'}</a>
						</p>
					</div>
				</div><!-- /end new_comment_form_content -->
			</div>
		</div>
	
		<p class="align_center">
			<a id="new_comment_tab_btn" href="#new_comment_form">{l s='Be the first to write your review' mod='productcomments'} !</a>
		</p>
		{else}
		<p class="align_center">{l s='No customer comments for the moment.' mod='productcomments'}</p>
		{/if}
	{/if}	
	</div>
</div>