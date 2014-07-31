<select name="payment_module_name" id="payment_module_name">
  {if !$PS_CATALOG_MODE}
    {foreach from=$payment_modules item='module'}
      <option value="{$module->name}" {if isset($smarty.post.payment_module_name) && $module->name == $smarty.post.payment_module_name}selected="selected"{/if}>{$module->displayName}</option>
    {/foreach}
  {else}
      <option value="{l s='Back-office order'}">{l s='Back-office order'}</option>
  {/if}
</select>
