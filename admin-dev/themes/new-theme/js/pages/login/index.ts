/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import 'prestakit/dist/js/prestashop-ui-kit';
import 'jquery-ui-dist/jquery-ui';

import LoginFormMap from '@pages/login/login-map';
import onReady from '@components/on-ready';
import EmailInput from '@components/email-input';

onReady(() => {
  const loginForm = document.querySelector<HTMLFormElement>(LoginFormMap.loginForm);
  const forgotPasswordForm = document.querySelector<HTMLFormElement>(LoginFormMap.forgotPasswordForm);

  document.querySelector(LoginFormMap.forgotPasswordLink)?.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopImmediatePropagation();
    loginForm?.classList.add('d-none');
    forgotPasswordForm?.classList.remove('d-none');
    // Hide all alert messages
    document.querySelectorAll<HTMLElement>(LoginFormMap.alertMessages).forEach((alert) => {
      alert.remove();
    });
  });

  document.querySelector(LoginFormMap.cancelResetPasswordButton)?.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopImmediatePropagation();
    loginForm?.classList.remove('d-none');
    forgotPasswordForm?.classList.add('d-none');
  });

  new EmailInput();
});
