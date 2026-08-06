{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="component-search-quickaccess d-none">
  <p class="component-search-title">{l s='Quick Access' d='Admin.Navigation.Header'}</p>
  {if $quick_access}
    {foreach $quick_access as $quick}
      <a class="dropdown-item quick-row-link{if $link->matchQuickLink({$quick.link|escape:'html':'UTF-8'})}{assign "matchQuickLink" $quick.id_quick_access} active{/if}"
         href="{$quick.link|escape:'html':'UTF-8'}"
        {if $quick.new_window} target="_blank"{/if}
         data-item="{$quick.name|escape:'html':'UTF-8'}"
      >{$quick.name|escape:'html':'UTF-8'}</a>
    {/foreach}
  {/if}
  <div class="dropdown-divider"></div>
  {if isset($matchQuickLink)}
    <a id="quick-remove-link"
      class="dropdown-item js-quick-link"
      href="#"
      data-method="remove"
      data-quicklink-id="{$matchQuickLink}"
      data-rand="{1|rand:200}"
      data-icon="{$quick_access_current_link_icon|escape:'html':'UTF-8'}"
      data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
      data-post-link="{$link->getAdminLink('AdminQuickAccesses')|escape:'html':'UTF-8'}"
      data-prompt-text="{l s='Please name this shortcut:' js=1 d='Admin.Navigation.Header'}"
      data-link="{$quick_access_current_link_name|truncate:32}"
    >
      <i class="material-icons">remove_circle_outline</i>
      {l s='Remove from Quick Access' d='Admin.Navigation.Header'}
    </a>
  {else}
    <a id="quick-add-link"
      class="dropdown-item js-quick-link"
      href="#"
      data-rand="{1|rand:200}"
      data-icon="{$quick_access_current_link_icon|escape:'html':'UTF-8'}"
      data-method="add"
      data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
      data-post-link="{$link->getAdminLink('AdminQuickAccesses')|escape:'html':'UTF-8'}"
      data-prompt-text="{l s='Please name this shortcut:' js=1  d='Admin.Navigation.Header'}"
      data-link="{$quick_access_current_link_name|truncate:32|escape:'html':'UTF-8'}"
    >
      <i class="material-icons">add_circle</i>
      {l s='Add current page to Quick Access'  d='Admin.Actions'}
    </a>
  {/if}
  <a id="quick-manage-link" class="dropdown-item" href="{$link->getAdminLink("AdminQuickAccesses")|escape:'html':'UTF-8'}">
    <i class="material-icons">settings</i>
    {l s='Manage your quick accesses' d='Admin.Navigation.Header'}
  </a>
</div>
