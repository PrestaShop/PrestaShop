{* Quick access *}
<div class="dropdown quick-accesses">
  <button class="btn btn-link btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="quick_select">
    {l s='Quick Access'}
  </button>
  <div class="dropdown-menu">
    {foreach $quick_access as $quick}
      <a class="dropdown-item{if $link->matchQuickLink({$quick.link})}{assign "matchQuickLink" $quick.id_quick_access} active{/if}"
         href="{$quick.link|escape:'html':'UTF-8'}"
        {if $quick.new_window} target="_blank"{/if}
         data-item="{$quick.name}"
      >{$quick.name}</a>
    {/foreach}
    <div class="dropdown-divider"></div>
    {if isset($matchQuickLink)}
      <a
        class="dropdown-item js-quick-link"
        href="#"
        data-method="remove"
        data-quicklink-id="{$matchQuickLink}"
        data-rand="{1|rand:200}"
        data-icon="{$quick_access_current_link_icon}"
        data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
        data-post-link="{$link->getAdminLink('AdminQuickAccesses')}"
        data-prompt-text="{l s='Please name this shortcut:' js=1}"
        data-link="{$quick_access_current_link_name|truncate:32}"
      >
        <i class="material-icons">remove_circle_outline</i>
        {l s='Remove from QuickAccess'}
      </a>
    {else}
      <a
        class="dropdown-item js-quick-link"
        href="#"
        data-rand="{1|rand:200}"
        data-icon="{$quick_access_current_link_icon}"
        data-method="add"
        data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
        data-post-link="{$link->getAdminLink('AdminQuickAccesses')}"
        data-prompt-text="{l s='Please name this shortcut:' js=1}"
        data-link="{$quick_access_current_link_name|truncate:32}"
      >
        <i class="material-icons">add_circle_outline</i>
        {l s='Add current page to QuickAccess'}
      </a>
    {/if}
    <a class="dropdown-item" href="{$link->getAdminLink("AdminQuickAccesses")|addslashes}">
      <i class="material-icons">settings</i>
      {l s='Manage quick accesses'}
    </a>
  </div>
</div>
