<h3>{l s='Stock is open' mod='dejala'}</h3>
{foreach from=$weekdayLabels item=weekday name=weekdayLoop}
	{assign var='curWeekdayIdx' value=$smarty.foreach.weekdayLoop.index}
	<div class="margin-form">
		<input type="checkbox" name="weekday_{$curWeekdayIdx}" value="1" {if (isset($calendar.entries.$curWeekdayIdx) && $calendar.entries.$curWeekdayIdx)}checked="checked"{/if}>
		{$weekday}: {l s='De' mod='dejala'}
		<select name="start_hour_{$curWeekdayIdx}">
		{if (isset($calendar.entries.$curWeekdayIdx))}
			{assign var='start_hour' value=$calendar.entries.$curWeekdayIdx.start_hour}
		{else}
			{assign var='start_hour' value=9}
		{/if}	
		{section name=starthour start=0 loop=24 step=1}
			<option value="{$smarty.section.starthour.index}" {if $start_hour == $smarty.section.starthour.index}selected="selected"{/if}>{$smarty.section.starthour.index} H</option>
		{/section}
		</select>
		{l s='A' mod='dejala'}
		<select name="stop_hour_{$curWeekdayIdx}">
		{if (isset($calendar.entries.$curWeekdayIdx))}
			{assign var='stop_hour' value=$calendar.entries.$curWeekdayIdx.stop_hour}
		{else}
			{assign var='stop_hour' value=18}
		{/if}	
		{section name=stophour start=0 loop=25 step=1}
			<option value="{$smarty.section.stophour.index}" {if $stop_hour == $smarty.section.stophour.index}selected="selected"{/if}>{$smarty.section.stophour.index} H</option>
		{/section}
		</select>

</div>

{/foreach}