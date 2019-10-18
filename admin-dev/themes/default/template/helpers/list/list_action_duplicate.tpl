{**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<a href="#" title="{$action|escape:'html':'UTF-8'}" onclick="{if $confirm}confirm_link('', '{$confirm|escape:'html':'UTF-8'}', '{l s='Yes' d='Admin.Global'}', '{l s='No' d='Admin.Global'}', '{$location_ok|escape:'html':'UTF-8'}', '{$location_ko|escape:'html':'UTF-8'}'){else}document.location = '{$location_ko|escape:'html':'UTF-8'}'{/if}">
	<i class="icon-copy"></i> {$action}
</a>
