<form action="#">
  <select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
    <option value="">{l s='All brands' d='Modules.Brandlist.Shop'}</option>
    {foreach from=$brands item=brand}
      <option value="{$brand['link']}">{$brand['name']}</option>
    {/foreach}
  </select>
</form>
