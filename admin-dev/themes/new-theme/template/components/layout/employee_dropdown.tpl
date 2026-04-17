{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if isset($employee)}
<div class="dropdown employee-dropdown">
  <div class="rounded-circle person" data-toggle="dropdown">
    <i class="material-icons">account_circle</i>
  </div>
  <div class="dropdown-menu dropdown-menu-right">
    <div class="employee-wrapper-avatar">
      <div class="employee-top">
        <span class="employee-avatar"><img class="avatar rounded-circle" src="{$employee->getImage()}" alt="{$employee->firstname}" /></span>
        <span class="employee_profile">{l s='Welcome back %name%' sprintf=['%name%' => $employee->firstname] d='Admin.Navigation.Header'}</span>
      </div>

      <a class="dropdown-item employee-link profile-link" href="{$link->getAdminLink('AdminEmployees', true, [], ['id_employee' => $employee->id|intval, 'updateemployee' => 1])|escape:'html':'UTF-8'}">
      <i class="material-icons">edit</i>
      <span>{l s='Your profile' d='Admin.Navigation.Header'}</span>
    </a>
    </div>

    <p class="divider"></p>

    {foreach from=$displayBackOfficeEmployeeMenu item=$menuItem}
      {assign var=menuItemProperties value=$menuItem->getProperties()}
        <a class="dropdown-item {$menuItem->getClass()}" href="{$menuItemProperties.link}" {if !isset($menuItemProperties.isExternalLink) || true === $menuItemProperties.isExternalLink} target="_blank"{/if} rel="noopener noreferrer nofollow">
            {if isset($menuItemProperties.icon)}<i class="material-icons">{$menuItemProperties.icon}</i> {/if}{$menuItem->getContent()}
        </a>
        {if $menuItem@last}
          <p class="divider"></p>
        {/if}
    {/foreach}

    <a class="dropdown-item employee-link text-center" id="header_logout" href="{$logout_link|escape:'html':'UTF-8'}">
      <i class="material-icons d-lg-none">power_settings_new</i>
      <span>{l s='Sign out' d='Admin.Navigation.Header'}</span>
    </a>
  </div>
</div>
{/if}
