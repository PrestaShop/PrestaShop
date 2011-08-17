{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
{literal}
	$('document').ready(function(){
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
					<span style="float:left;">{l s='Average grade' mod='productcomments'}&nbsp</span>
					{section name="i" start=0 loop=5 step=1}
						{if $comment.grade le $smarty.section.i.index}
							<div class="star"></div>
						{else}
							<div class="star star_on"></div>
						{/if}
					{/section}
					<br style="clear:both"/>
					<div>
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
		<p class="align_center">{l s='No customer comments for the moment.' mod='productcomments'}</p>
	{/if}	
	</div>
</div>