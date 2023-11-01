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

{if isset($display_multishop_checkbox) && $display_multishop_checkbox}
<div class="bootstrap form-horizontal panel">
	<div class="panel-heading"><i class="icon-cogs"></i> {l s='Configuration' d='Admin.Global'}</div>
  <div class="form-wrapper">
    <div class="form-group">
      <label class="control-label col-lg-4">
        {l s='Activate module for this shop context: %s.' sprintf=[$shop_context] d='Admin.Modules.Notification' html=true}
      </label>
      <div class="col-lg-8">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="activateModule" id="module_shop_enabled_on" value="1"{if $module->isEnabledForShopContext()} checked="checked"{/if} onclick="location.href = '{$multishop_enable_url}'">
          <label for="module_shop_enabled_on">Yes</label>
          <input type="radio" name="activateModule" id="module_shop_enabled_off" value="0"{if !$module->isEnabledForShopContext()} checked="checked"{/if} onclick="location.href = '{$multishop_disable_url}'">
          <label for="module_shop_enabled_off">No</label>
          <a class="slide-button btn"></a>
        </span>
      </div>
    </div>
  </div>
</div>
{/if}
