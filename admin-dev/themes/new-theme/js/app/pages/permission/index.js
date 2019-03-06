/**
 * 2007-2019 PrestaShop and Contributors
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
 */
import Vue from 'vue';
import Permission from './permission.vue';

const $ = window.$;

/**
 * Vue component displaying a permission tree.
 */
class PermissionApp {
  constructor(profileId, target, userPermissions, updateKey) {
    if ($(target).length === 0) {
      return;
    }

    // If the selector cannot be found, we do not load the Vue app
    this.vm = new Vue({
      el: target,
      template: '<permission :update-key="updateKey" :profile-id="profileId" :update-url="updateUrl" :messages="messages" :permissions="permissions" :types="types" :user-permissions="userPermissions" :canEdit="canEdit" />',
      data: {
        profileId,
        messages: window.permissionsMessages,
        permissions: $(target).data('permissions'),
        updateKey,
        userPermissions,
        updateUrl: $(target).data('update-url'),
        canEdit: $(target).data('can-edit'),
        types: $(target).data('types'),
      },
      components: {
        Permission,
      },
    });
  }
}

export default PermissionApp;
