/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PermissionApp from '@app/pages/permission/index';

const {$} = window;

$(() => {
  $('.js-permissions-content').each((i: number, element: HTMLElement): void => {
    new PermissionApp(
      $(element).data('profile-id'),
      `#profile-content-${$(element).data('profile-id')}`,
      'tab_id',
      $(element).data('profile-permissions'),
      $(element).data('employee-permissions'),
    );

    new PermissionApp(
      $(element).data('profile-id'),
      `#module-content-${$(element).data('profile-id')}`,
      'id_module',
      $(element).data('modules-permissions'),
      $(element).data('employee-modules-permissions'),
    );
  });
});
