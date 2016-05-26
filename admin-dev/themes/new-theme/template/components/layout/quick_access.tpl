{* Quick access *}
<div class="ps-dropdown dropdown">
  <span type="button" id="quick-access" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="selected-item">{l s='Quick Access'}</span> <i class="material-icons arrow-down">keyboard_arrow_down</i>
  </span>
  <div class="ps-dropdown-menu dropdown-menu" aria-labelledby="quick-access">
    {foreach $quick_access as $quick}
      <a href="{$baseAdminUrl}{$quick.link|escape:'html':'UTF-8'}" class="dropdown-item" data-item="{$quick.name}">{$quick.name}</a></li>
    {/foreach}
    <hr>
    {if isset($matchQuickLink)}
      <a
         class="dropdown-item js-quick-link"
         data-method="remove"
         data-quicklink-id="{$matchQuickLink}"
         data-rand="{1|rand:200}"
         data-icon="{$quick_access_current_link_icon}"
         data-url="{$link->getQuickLink($smarty.server['REQUEST_URI'])}"
         data-post-link="{$link->getAdminLink('AdminQuickAccesses')}"
         data-prompt-text="{l s='Please name this shortcut:' js=1}"
         data-link="{$quick_access_current_link_name|truncate:32}"
      >
        {l s='Remove from QuickAccess'}
      </a>
    {/if}
    <a
      class="dropdown-item js-quick-link"
      data-rand="{1|rand:200}"
      data-icon="{$quick_access_current_link_icon}"
      data-method="add"
      data-url="{$link->getQuickLink($smarty.server['REQUEST_URI'])}"
      data-post-link="{$link->getAdminLink('AdminQuickAccesses')}"
      data-prompt-text="{l s='Please name this shortcut:' js=1}"
      data-link="{$quick_access_current_link_name|truncate:32}"
    >
      <i class="material-icons">add_circle_outline</i>
      {l s='Add current page to QuickAccess'}
    </a>
    <a class="dropdown-item" href="{$link->getAdminLink("AdminQuickAccesses")|addslashes}">
      <i class="material-icons">settings</i>
      {l s='Manage quick accesses'}
    </a>
  </div>
</div>
