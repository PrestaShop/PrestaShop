<select name="days" id="days">
  <option value="">-</option>
  {foreach from=$dates.days item=v}
    <option value="{$v}" {if ($dates.sl_day == $v)}selected="selected"{/if}>{$v}</option>
  {/foreach}
</select>

<select id="months" name="months">
  <option value="">-</option>
  {foreach from=$dates.months key=k item=v}
    <option value="{$k}" {if ($dates.sl_month == $k)}selected="selected"{/if}>{$v}</option>
  {/foreach}
</select>

<select id="years" name="years">
  <option value="">-</option>
  {foreach from=$dates.years item=v}
    <option value="{$v}" {if ($dates.sl_year == $v)}selected="selected"{/if}>{$v}</option>
  {/foreach}
</select>
