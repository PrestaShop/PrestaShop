{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- Products review-->
<div id="bordered_products_review" class="col-xs-12 col-sm-8 col-md-8">	
	<div id="border_wave_top" class="col-xs-12 col-md-12"></div>
	<div id="products_review">	
		<h6>{l s='Products review' mod='productcomments'}</h6>
		{*<button type="button" class="view">{l s='View all' mod='productcomments'}</button>*}
		<div class="prod_rev">
			{if $comments}
				{$y = 0}
				{foreach from=$comments item=comment}
					{if $comment.content}
					<div class="review col-xs-12 col-md-6 col-sm-6">
						{$y=$y+1}
						<a href="{$urls.$y}"><img src="{$productcomment_cover_images.$y}" id="review_img" class="col-xs-4 col-sm-4 col-md-3" height="{$mediumSize.height}" width="{$mediumSize.width}"/></a>
						<div id="review_info" class="col-xs-7 col-sm-8 col-md-9">
							<a href="{$urls.$y}"><h5 class="col-xs-12 col-sm-12 col-md-12">{$comment.name}</h5></a>
							<span class="col-xs-12 col-sm-12 col-md-12"><b>{l s='Posted' mod='productcomments'}</b>: {dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0} </span>
							<span class="col-xs-12 col-sm-12 col-md-12"><b>{l s='Reviewer' mod='productcomments'}</b>: {$comment.customer_name} </span>
							<div class="overall col-xs-12 col-sm-12 col-md-12"/>
								<span class="col-xs-4 col-sm-3 col-md-3">{l s='Overall' mod='productcomments'}:</span>
								<div class="star_content clearfix col-xs-8 col-sm-9 col-md-9">
								{section name="i" start=0 loop=5 step=1}
									{if $comment.grade le $smarty.section.i.index}
										<div class="star"></div>
									{else}
										<div class="star star_on"></div>
									{/if}
								{/section}
								</div>
							</div>
						</div>
						<p class="prod_desc col-xs-12 col-sm-12 col-md-12">{$comment.content}</p>
					</div>	
					{/if}
				{/foreach}
			{else}
			No comments,bro!
			{/if}
		</div>	
	</div>
	<div id="border_wave_bottom" class="col-xs-12 col-md-12"></div>
</div>


<!-- /Products review-->