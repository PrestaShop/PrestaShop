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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
$(function(){
	$('a[href=#idTab5]').click(function(){
		$('*[id^="idTab"]').addClass('block_hidden_only_for_screen');
		$('div#idTab5').removeClass('block_hidden_only_for_screen');

		$('ul#more_info_tabs a[href^="#idTab"]').removeClass('selected');
		$('a[href="#idTab5"]').addClass('selected');
	});
});
</script>

{if (!$content_only && (($nbComments == 0 && $too_early == false && $logged == 1) || ($nbComments != 0)))}
<div id="product_comments_block_extra" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	{if $nbComments != 0}
		<div class="comments_note clearfix">
			<span>{l s='Rating' mod='productcomments'}&nbsp;</span>
			<div class="star_content clearfix">
				{section name="i" start=0 loop=5 step=1}
					{if $averageTotal le $smarty.section.i.index}
						<div class="star"></div>
					{else}
						<div class="star star_on"></div>
					{/if}
				{/section}
				<meta itemprop="worstRating" content = "0">
				<meta itemprop="ratingValue" content = "2">
				<meta itemprop="bestRating" content = "5">
				<span class="hidden" itemprop="ratingValue">{$averageTotal}</span> 
			</div>
		</div> <!-- .comments_note -->
	{/if}

	<ul class="comments_advices">
		{if $nbComments != 0}
			<li>
				<a href="#idTab5" class="reviews">
					{l s='Read reviews' mod='productcomments'} (<span itemprop="reviewCount">{$nbComments}</span>)
				</a>
			</li>
		{/if}
		{if ($too_early == false AND ($logged OR $allow_guests))}
			<li>
				<a class="open-comment-form" href="#new_comment_form">
					{l s='Write a review' mod='productcomments'}
				</a>
			</li>
		{/if}
	</ul>
</div>
{/if}
<!--  /Module ProductComments -->
