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
{if isset($employee)}
<div class="dropdown employee-dropdown">
  <div class="rounded-circle person" data-toggle="dropdown">
    <i class="material-icons">account_circle</i>
  </div>
  <div class="dropdown-menu dropdown-menu-right">
    <div class="employee-wrapper-avatar">
      
      <span class="employee_avatar"><img class="avatar rounded-circle" src="{$employee->getImage()}" /></span>
      <span class="employee_profile">{l s='Welcome back %name%' sprintf=['%name%' => $employee->firstname] d='Admin.Navigation.Header'}</span>
      <a class="dropdown-item employee-link profile-link" href="{$link->getAdminLink('AdminEmployees', true, [], ['id_employee' => $employee->id|intval, 'updateemployee' => 1])|escape:'html':'UTF-8'}">
      <i class="material-icons">settings</i>
      {l s='Your profile' d='Admin.Navigation.Header'}
    </a>
    </div>
    
    <p class="divider"></p>
    <a class="dropdown-item" href="{l s='https://www.prestashop.com/en/resources/documentations?utm_source=back-office&utm_medium=profile&utm_campaign=resources-en&utm_content=download17' d='Admin.Navigation.Header'}" target="_blank"><i class="material-icons">book</i> {l s='Resources' d='Admin.Navigation.Header'}</a>
    <a class="dropdown-item" href="{l s='https://www.prestashop.com/en/training?utm_source=back-office&utm_medium=profile&utm_campaign=training-en&utm_content=download17' d='Admin.Navigation.Header'}" target="_blank"><i class="material-icons">school</i> {l s='Training' d='Admin.Navigation.Header'}</a>
    <a class="dropdown-item" href="{l s='https://www.prestashop.com/en/experts?utm_source=back-office&utm_medium=profile&utm_campaign=expert-en&utm_content=download17' d='Admin.Navigation.Header'}" target="_blank"><i class="material-icons">person_pin_circle</i> {l s='Find an Expert' d='Admin.Navigation.Header'}</a>
    <a class="dropdown-item" href="{l s='https://addons.prestashop.com?utm_source=back-office&utm_medium=profile&utm_campaign=addons-en&utm_content=download17' d='Admin.Navigation.Header'}" target="_blank"><i class="material-icons">extension</i> {l s='PrestaShop Marketplace' d='Admin.Navigation.Header'}</a>
    <a class="dropdown-item" href="{l s='https://www.prestashop.com/en/contact?utm_source=back-office&utm_medium=profile&utm_campaign=help-center-en&utm_content=download17' d='Admin.Navigation.Header'}" target="_blank"><i class="material-icons">help</i> {l s='Help Center' d='Admin.Navigation.Header'}</a>
    <p class="divider"></p>
    <a class="dropdown-item employee-link text-center" id="header_logout" href="{$logout_link|escape:'html':'UTF-8'}">
      <i class="material-icons d-lg-none">power_settings_new</i>
      <span>{l s='Sign out' d='Admin.Navigation.Header'}</span>
    </a>
  </div>
</div>
{/if}
