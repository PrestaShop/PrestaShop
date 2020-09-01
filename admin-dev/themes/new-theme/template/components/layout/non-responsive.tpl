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
<div id="non-responsive" class="js-non-responsive">
  <h1>{l s='Oh no!'}</h1>
  <p class="mt-3">
    {l s='The mobile version of this page is not available yet.'}
  </p>
  <p class="mt-2">
    {l s='Please use a desktop computer to access this page, until is adapted to mobile.'}
  </p>
  <p class="mt-2">
    {l s='Thank you.'}
  </p>
  <a href="{$default_tab_link|escape:'html':'UTF-8'}" class="btn btn-primary py-1 mt-3">
    <i class="material-icons">arrow_back</i>
    {l s='Back' d='Admin.Global'}
  </a>
</div>
