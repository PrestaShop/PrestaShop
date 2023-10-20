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

<!--start filter module-->
<form method="post" class="form-inline">
  <div class="row">
    <div class="col-lg-8">
      <div class="form-group">
        <label>{l s='Filter by'}</label>
        <select name="module_install" id="module_install_filter" class="form-control {if isset($showInstalledModules) && $showInstalledModules && $showInstalledModules != 'installedUninstalled' }active{/if}">
          <option value="installedUninstalled" {if $showInstalledModules eq 'installedUninstalled'}selected="selected"{/if}>{l s='Installed & Not Installed'}</option>
          <option value="installed" {if $showInstalledModules eq 'installed'}selected="selected"{/if}>{l s='Installed Modules'}</option>
          <option value="uninstalled" {if $showInstalledModules eq 'uninstalled'}selected="selected"{/if}>{l s='Modules Not Installed '}</option>
        </select>
      </div>

      <div class="form-group">
        <select name="module_status" id="module_status_filter" class="form-control {if isset($showEnabledModules) && $showEnabledModules && $showEnabledModules != enabledDisabled}active{/if}">
          <option value="enabledDisabled" {if $showEnabledModules eq 'enabledDisabled'}selected="selected"{/if}>{l s='Enabled & Disabled'}</option>
          <option value="enabled" {if $showEnabledModules eq 'enabled'}selected="selected"{/if}>{l s='Enabled Modules'}</option>
          <option value="disabled" {if $showEnabledModules eq 'disabled'}selected="selected"{/if}>{l s='Disabled Modules'}</option>
        </select>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="form-group">
        <label>{l s='Authors'}</label>
        <select class="filter {if isset($showTypeModules) && $showTypeModules && $showTypeModules != allModules}active{/if}" name="module_type" id="module_type_filter">
          <option value="allModules" {if $showTypeModules eq 'allModules'}selected="selected"{/if}>{l s='All authors'}</option>
          {foreach from=$list_modules_authors key=module_author item=status}
            <option value="authorModules[{$module_author}]" {if $status eq "selected"}selected{/if}>{$module_author|truncate:20:'...'}</option>
          {/foreach}
        </select>
      </div>
    </div>
  </div>
</form>
<!--end filter module-->
