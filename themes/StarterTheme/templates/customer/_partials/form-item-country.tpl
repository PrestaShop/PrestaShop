<select id="id_country" name="id_country" {if $required}required{/if}>
  {foreach $countries as $country}
    <option value="{$country['id_country']}" {if $country.id_country == $sl_country}selected="selected"{/if}>
      {$country.name}
    </option>
  {/foreach}
</select>
