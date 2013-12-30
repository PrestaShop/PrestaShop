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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript" src="{$module_dir}js/products-comparison.js"></script>
<script type="text/javascript" src="{$module_dir}js/jquery.rating.pack.js"></script>
<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}jquery/jquery.cluetip.js"></script>
<script type="text/javascript">
	{literal}
	$(function()
	{
		$('input[type=radio].star').rating();
	});
	$(function()
	{
		$('.auto-submit-star').rating();
	});
	
	//close  comment form
	function closeCommentForm()
	{
		$('#sendComment').slideUp('fast');
		$('input#addCommentButton').fadeIn('slow');
	}
	{/literal}
</script>

<tr class="comparison_header">
	<td class="feature-name">
		<strong>{l s='Comments' mod='productcomments'}</strong>
	</td>
	{section loop=$list_ids_product|count step=1 start=0 name=td}
		<td></td>
	{/section}
</tr>

{foreach from=$grades item=grade key=grade_id}
<tr>
	{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
	<td class="{$classname} feature-name">
		<strong>{$grade}</strong>
	</td>
	{foreach from=$list_ids_product item=id_product}
		{assign var='tab_grade' value=$product_grades[$grade_id]}
		<td  width="{$width}%" class="{$classname} comparison_infos ajax_block_product" align="center">
			{if isset($tab_grade[$id_product]) AND $tab_grade[$id_product]}
				<div class="product-rating">
					{section loop=6 step=1 start=1 name=average}
						<input class="auto-submit-star" disabled="disabled" type="radio" name="{$grade_id}_{$id_product}_{$smarty.section.average.index}" {if isset($tab_grade[$id_product]) AND $tab_grade[$id_product]|round neq 0 and $smarty.section.average.index eq $tab_grade[$id_product]|round}checked="checked"{/if} />
					{/section}
				</div>
			{else}
				-
			{/if}
		</td>
	{/foreach}
</tr>				
{/foreach}

	{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
<tr>
	<td  class="{$classname} feature-name">
		<strong>{l s='Average' mod='productcomments'}</strong>
	</td>
{foreach from=$list_ids_product item=id_product}
	<td  width="{$width}%" class="{$classname} comparison_infos" align="center" >
		{if isset($list_product_average[$id_product]) AND $list_product_average[$id_product]}
			<div class="product-rating">
				{section loop=6 step=1 start=1 name=average}
					<input class="auto-submit-star" disabled="disabled" type="radio" name="average_{$id_product}" {if $list_product_average[$id_product]|round neq 0 and $smarty.section.average.index eq $list_product_average[$id_product]|round}checked="checked"{/if} />
				{/section}
			</div>
		{else}
			-
		{/if}
	</td>	
{/foreach}
</tr>

<tr>
	<td class="{$classname} comparison_infos feature-name">&nbsp;</td>
	{foreach from=$list_ids_product item=id_product}
	<td  width="{$width}%" class="{$classname} comparison_infos" align="center" >
		{if isset($product_comments[$id_product]) AND $product_comments[$id_product]}
			<script type="text/javascript">
			$(document).ready(function() {
				var htmlContent = $('#comments_{$id_product}').html();
				$("[rel=#comments_{$id_product}]").popover({
					placement : 'top', //placement of the popover. also can use top, bottom, left or right
					title : false, //this is the top title bar of the popover. add some basic css
					html: 'true', //needed to show html of course
					content : htmlContent  //this is the content of the html box. add the image here or anything you want really.
				});
			});
			</script>
			<a 
			class="btn btn-default button button-small" 
			href="#" 
			onclick="return false;" 
			rel="#comments_{$id_product}">
				<span>
					{l s='View comments' mod='productcomments'}<i class="icon-chevron-right right"></i>
				</span>
			</a>
			<div style="display:none" id="comments_{$id_product}">   
				{foreach from=$product_comments[$id_product] item=comment}	
					<div class="well well-sm">
						<strong>{$comment.customer_name|escape:'html':'UTF-8'} </strong>
						<small class="date"> {dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</small> 
						<div class="comment_content">{$comment.content|escape:'html':'UTF-8'|nl2br}</div>
					</div>
				{/foreach}
			</div>
		{else}
			-
		{/if}
	</td>	
{/foreach}
</tr>