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
*  @version  Release: $Revision$
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
	
	//close  comment form
	function closeCommentForm(){ldelim}
		$('#sendComment').slideUp('fast');
		$('input#addCommentButton').fadeIn('slow');
	{rdelim}

	$('document').ready(function(){literal}{{/literal}
		$('#new_comment_btn').fancybox({literal}{{/literal}
			'hideOnContentClick': false,
			'onClosed': function(){literal}{{/literal}
			{literal}}{/literal},
		{literal}}{/literal});
	
		$('a[href=#idTab5]').click(function(){literal}{{/literal}
			$('*[id^="idTab"]').addClass('block_hidden_only_for_screen');
			$('div#idTab5').removeClass('block_hidden_only_for_screen');
			
			$('ul#more_info_tabs a[href^="#idTab"]').removeClass('selected');
			$('a[href="#idTab5"]').addClass('selected');
		{literal}}{/literal});
	{literal}}{/literal});
</script>

<div id="product_comments_block_extra">
	<div>
		<span style="float:left;">{l s='Average grade' mod='productcomments'}&nbsp</span>
		{section name="i" start=0 loop=5 step=1}
			{if $averageTotal le $smarty.section.i.index}
				<div class="star"></div>
			{else}
				<div class="star star_on"></div>
			{/if}
		{/section}
		<br style="clear:both"/>
	</div>
	<div>
		<a href="#idTab5">{l s='Read user reviews' mod='productcomments'} ({$nbComments})</a><br/>
	{if ($too_early == false AND ($cookie->isLogged() == true OR $allow_guests == true))}
		{if $cookie->isLogged() == true || $allow_guests == true}
			<a id="new_comment_btn" href="#new_comment_form">{l s='Give your advice' mod='productcomments'}</a>
		{/if}
	{/if}
	</div>
	<div style="display: none;">
		<div id="new_comment_form">
			<div>
				<form action="{$action_url}" method="post" id="sendComment">
					<span class="title">{l s='Give your advice' mod='productcomments'}</span>
					<div style="margin-top:20px">
						<img style="float:left" src="{$link->getImageLink($product->link_rewrite, $productcomment_cover, 'medium')}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$product->name|escape:html:'UTF-8'}" />
						<div style="float:left; width:300px; margin-left:10px">
							<strong class="clearfix">{$product->name}</strong>
							{$product->description_short}
						</div>
					</div>
					<br style="clear:both"/>
					<div style="margin-top:20px">
						{if $criterions|@count > 0}
						<table border="0" cellspacing="0" cellpadding="0">
						{section loop=$criterions name=i start=0 step=1}
						<tr>
							<th>
								<input type="hidden" name="id_product_comment_criterion_{$smarty.section.i.iteration}" value="{$criterions[i].id_product_comment_criterion|intval}" />
								{$criterions[i].name|escape:'html':'UTF-8'}:&nbsp;
							</th>
							<td>
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" id="{$smarty.section.i.iteration}_grade" value="1" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="2" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="3" checked="checked" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="4" />
								<input class="star" type="radio" name="{$smarty.section.i.iteration}_grade" value="5" />
							</td>
						</tr>
						{/section}
						</table>
						{/if}
						<label for="comment_title">{l s='Title' mod='productcomments'} *:</label>
						<input name="title" type="text" value=""/><br/>
						<label for="content">{l s='Comment' mod='productcomments'} *:</label>
						<textarea name="content"></textarea>
						{if $allow_guests == true && $cookie->isLogged() == false}
						<label>{l s='Your name:' mod='productcomments'} *:</label>
						<input name="customer_name" type="text" value=""/><br/>
						{/if}
						<p style="float:right">
							<button name="submitMessage" type="submit">{l s='Send' mod='productcomments'}</button>&nbsp;
							{l s='or' mod='productcomments'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='productcomments'}</a>
						</p>
						<br style="clear:both"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>