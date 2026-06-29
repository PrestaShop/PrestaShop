{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

<div class="dropdown quick-accesses">
  <button class="btn btn-link btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="quick_select">
    {l s='Quick Access' d='Admin.Navigation.Header'}
  </button>
  <div class="dropdown-menu">
    {if $quick_access}
      {foreach $quick_access as $quick}
        <a class="dropdown-item quick-row-link {if isset($quick.class)}{$quick.class|escape:'html':'UTF-8'}{/if}{if $link->matchQuickLink({$quick.link})}{assign "matchQuickLink" $quick.id_quick_access} active{/if}"
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
        data-quicklink-id="{$matchQuickLink|escape:'html':'UTF-8'}"
        data-rand="{1|rand:200}"
        data-icon="{$quick_access_current_link_icon|escape:'html':'UTF-8'}"
        data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
        data-post-link="{$link->getAdminLink('AdminQuickAccesses')|escape:'html':'UTF-8'}"
        data-prompt-text="{l s='Please name this shortcut:' js=1 d='Admin.Navigation.Header'}"
        data-link="{$quick_access_current_link_name|truncate:32|escape:'html':'UTF-8'}"
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
        data-link="{$quick_access_current_link_name|escape:'html':'UTF-8'}"
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
</div>

<div class="modal fade" id="quick-access-add-modal" tabindex="-1" role="dialog"
     aria-labelledby="quick-access-add-modal-title" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="quick-access-add-modal-title">
          {l s='Add to Quick Access' d='Admin.Navigation.Header'}
        </h4>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="{l s='Close' d='Admin.Actions'}">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger d-print-none d-none" role="alert" id="quick-access-add-error">
          <div class="alert-text"></div>
        </div>
        <div class="form-group" id="quick-access-name-group">
          <label class="form-control-label" for="quick-access-name">
            {l s='Shortcut name' d='Admin.Navigation.Header'}
          </label>
          <input type="text" id="quick-access-name" class="form-control" required aria-required="true" maxlength="32"
                 data-required-message="{l s='Shortcut name is required' d='Admin.Navigation.Header'}">
          <div class="d-inline-block align-baseline text-danger mt-1 d-none" role="alert" id="quick-access-name-error">
            <i class="material-icons form-error-icon">error_outline</i>
            <span class="js-error-text"></span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-control-label d-block">
            {l s='Open in new window' d='Admin.Navigation.Header'}
          </label>
          <span class="ps-switch">
            <input id="quick-access-new-window-off" name="quick_access_new_window" value="0" checked type="radio">
            <label for="quick-access-new-window-off">{l s='No' d='Admin.Global'}</label>
            <input id="quick-access-new-window-on" name="quick_access_new_window" value="1" type="radio">
            <label for="quick-access-new-window-on">{l s='Yes' d='Admin.Global'}</label>
            <span class="slide-button"></span>
          </span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal">
          {l s='Cancel' d='Admin.Actions'}
        </button>
        <button type="button" class="btn btn-primary btn-lg" id="quick-access-save-btn">
          {l s='Save' d='Admin.Actions'}
        </button>
      </div>
    </div>
  </div>
</div>
