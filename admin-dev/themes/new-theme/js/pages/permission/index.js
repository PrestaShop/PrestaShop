/**
 * 2007-2019 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import PermissionApp from '../../app/pages/permission/index';

const $ = window.$;

$(document).ready(() => {
  $('.js-permissions-content').each((i, element) => {
    new PermissionApp(
      $(element).data('profile-id'),
      `#profile-content-${$(element).data('profile-id')}`,
      'tab_id',
      $(element).data('profile-permissions')
    );

    new PermissionApp(
      $(element).data('profile-id'),
      `#module-content-${$(element).data('profile-id')}`,
      'id_module',
      $(element).data('modules-permissions'),
      $(element).data('employee-modules-permissions')
    );
  });
});
