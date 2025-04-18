{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<div class="component-search-quickaccess d-none">
  <p class="component-search-title">{l s='Quick Access' d='Admin.Navigation.Header'}</p>
  {if $quick_access}
    {foreach $quick_access as $quick}
      <a class="dropdown-item quick-row-link{if $link->matchQuickLink({$quick.link})}{assign "matchQuickLink" $quick.id_quick_access} active{/if}"
         href="{$quick.link|escape:'html':'UTF-8'}"
        {if $quick.new_window} target="_blank"{/if}
         data-item="{$quick.name}"
      >{$quick.name}</a>
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
      data-icon="{$quick_access_current_link_icon}"
      data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
      data-post-link="{$link->getAdminLink('AdminQuickAccesses')}"
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
      data-icon="{$quick_access_current_link_icon}"
      data-method="add"
      data-url="{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}"
      data-post-link="{$link->getAdminLink('AdminQuickAccesses')}"
      data-prompt-text="{l s='Please name this shortcut:' js=1  d='Admin.Navigation.Header'}"
      data-link="{$quick_access_current_link_name|truncate:32}"
    >
      <i class="material-icons">add_circle</i>
      {l s='Add current page to Quick Access'  d='Admin.Actions'}
    </a>
  {/if}
  <a id="quick-manage-link" class="dropdown-item" href="{$link->getAdminLink("AdminQuickAccesses")|addslashes}">
    <i class="material-icons">settings</i>
    {l s='Manage your quick accesses' d='Admin.Navigation.Header'}
  </a>
</div>
