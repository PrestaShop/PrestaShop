{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="employee-dropdown dropdown">
  {if isset($employee)}
    <div class="person" data-toggle="dropdown">
      <i class="material-icons">account_circle</i>
    </div>
  {/if}
  <div class="dropdown-menu dropdown-menu-right">
    <div class="text-center employee_avatar">
      <i class="material-icons">account_circle</i>
      <span>{$employee->firstname} {$employee->lastname}</span>
    </div>
    <div>
      <a class="employee-link profile-link" href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&amp;id_employee={$employee->id|intval}&amp;updateemployee">
        <i class="material-icons">settings_applications</i> {l s='Your profile'}
      </a>
    </div>
    <div>
      <a class="employee-link" id="header_logout" href="{$login_link|escape:'html':'UTF-8'}&amp;logout">
          <i class="material-icons">power_settings_new</i> <span>{l s='Sign out'}</span>
      </a>
    </div>
  </div>
</div>
