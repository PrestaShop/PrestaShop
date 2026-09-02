/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
