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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
