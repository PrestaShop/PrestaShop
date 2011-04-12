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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="idTab5">
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
</script>
{if $comments}
	{if $criterions|@count > 0}
		<h2>{l s='Average grade' mod='productcomments'}</h2>
		<div style="float: left">
			{l s='Average' mod='productcomments'}:<br />
			{section loop=6 step=1 start=1 name=average}
				<input class="auto-submit-star" disabled="disabled" type="radio" name="average" {if $averageTotal|round neq 0 and $smarty.section.average.index eq $averageTotal|round}checked="checked"{/if} />
			{/section}
		</div>
		<div style="float: left; margin-left: 40px; width: 400px">
		{foreach from=$criterions item=c}
			<div style="float: left; margin-left: 20px; margin-bottom: 10px;">
			{$c.name|escape:'html':'UTF-8'}<br />
			{section loop=6 step=1 start=1 name=average}
				<input class="auto-submit-star" disabled="disabled" type="radio" name="{$c.name|escape:'html':'UTF-8'}_{$smarty.section.average.index}" value="{$smarty.section.average.index}" {if $averages[$c.id_product_comment_criterion]|round neq 0 and $smarty.section.average.index eq $averages[$c.id_product_comment_criterion]|round}checked="checked"{/if} />
			{/section}
			</div>
		{/foreach}
		</div>
	{/if}
	<div class="clear table_block">
		<table class="std" style="width: 100%">
			<thead>
				<tr>
					<th class="first_item" style="width:80px;">{l s='From' mod='productcomments'}</th>
					<th class="item">{l s='Title' mod='productcomments'}</th>
					<th class="item">{l s='Comment' mod='productcomments'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$comments item=comment}
				{if $comment.content}
				<tr>
					<td style="vertical-align:top">
						{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}
						{$comment.customer_name|escape:'html':'UTF-8'}.
					</td>
					<td style="vertical-align:top">
						{$comment.title}
					</td>
					<td style="vertical-align: top">
						{$comment.content|escape:'html':'UTF-8'|nl2br}
					</td>
				</tr>
				{/if}
			{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="align_center">{l s='No customer comments for the moment.' mod='productcomments'}</p>
{/if}

{if $too_early == true}
	<p class="align_center">{l s='You should wait' mod='productcomments'} {$delay} {l s='second(s) before posting a new comment' mod='productcomments'}</p>
{elseif $cookie->isLogged() == true || $allow_guests == true}
<p class="align_center"><input style="margin:auto;" class="button_large" type="button" id="addCommentButton" value="{l s='Add a comment' mod='productcomments'}" onclick="$('#sendComment').slideDown('slow');$(this).slideUp('slow');" /></p>
<form action="{$action_url}" method="post" class="std" id="sendComment" style="display:none;">
	<fieldset>
		<p class="align_right"><a href="javascript:closeCommentForm()">X</a></p>
		<p class="bold">{l s='Add a comment' mod='productcomments'}</p>
		{if $criterions|@count > 0}
		<table border="0" cellspacing="0" cellpadding="0">
		{section loop=$criterions name=i start=0 step=1}
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>
				<input type="hidden" name="id_product_comment_criterion_{$smarty.section.i.iteration}" value="{$criterions[i].id_product_comment_criterion|intval}" />
				{$criterions[i].name|escape:'html':'UTF-8'}
			</td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
		{if $allow_guests == true && $cookie->isLogged() == false}<p><label for="customer_name">{l s='Your name:' mod='productcomments'}</label><input type="text" name="customer_name" id="customer_name" /></p>{/if}
		<p><label for="comment_title">{l s='Title:' mod='productcomments'}</label><input type="text" name="title" id="comment_title" /></p>
		<p><label for="content">{l s='Comment:' mod='productcomments'}</label><textarea cols="46" rows="5" name="content" id="content"></textarea></p>
		<p class="submit">
			<input class="button" name="submitMessage" value="{l s='Send' mod='productcomments'}" type="submit" />
		</p>
	</fieldset>
</form>
{else}
	<p class="align_center">{l s='Only registered users can post a new comment.' mod='productcomments'}</p>
{/if}
</div>