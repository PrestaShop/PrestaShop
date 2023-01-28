/**
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
 */
import {App, createApp} from 'vue';
import Permission from './permission.vue';

const {$} = window;

/**
 * Vue component displaying a permission tree.
 */
class PermissionApp {
  vm?: App;

  constructor(
    profileId: string,
    target: string, permissionKey: string,
    profilePermissions: Record<string, any>,
    employeePermissions: string,
  ) {
    // If the selector cannot be found, we do not load the Vue app
    if ($(target).length === 0) {
      return;
    }

    this.vm = createApp(Permission, {
      data: () => ({
        profileId,
        permissionKey,
        profilePermissions,
        canEdit: $(target).data('can-edit'),
        employeePermissions: employeePermissions || {},
        messages: window.permissionsMessages,
        permissions: $(target).data('permissions'),
        types: $(target).data('types'),
        title: $(target).data('title'),
        emptyData: $(target).data('empty'),
        updateUrl: $(target).data('update-url'),
      }),
    });

    this.vm.mount(target);
  }
}

export default PermissionApp;
