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
<div id="product_comments_block_extra" class="bordercolor">
<ul>
	{if $nbComments != 0}
	<li class="comments_note">
		<span>{l s='Average grade' mod='productcomments'}&nbsp;</span>
		<div class="star_content clearfix">
		{section name="i" start=0 loop=5 step=1}
			{if $averageTotal le $smarty.section.i.index}
				<div class="star"></div>
			{else}
				<div class="star star_on"></div>
			{/if}
		{/section}
		</div>
	</li>
	{/if}
	{if $nbComments != 0}
	<li><a href="#idTab5">{l s='Read user reviews' mod='productcomments'} ({$nbComments})</a></li>
	{/if}
	{if ($too_early == false AND ($logged OR $allow_guests))}
	<li><a class="open-comment-form" href="#new_comment_form">{l s='Write your review' mod='productcomments'}</a></li>
	{/if}
</ul>
{*
	{if $nbComments != 0}
	<div class="comments_note">
		<span>{l s='Average grade' mod='productcomments'}&nbsp;</span>
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
		<a href="#idTab5">{l s='Read user reviews' mod='productcomments'} ({$nbComments})</a>
		{/if}
		{if ($too_early == false AND ($logged OR $allow_guests))}
		<a class="open-comment-form" href="#new_comment_form">{l s='Write your review' mod='productcomments'}</a>
		{/if}
	</div>
*}
</div>
<!--  /Module ProductComments -->