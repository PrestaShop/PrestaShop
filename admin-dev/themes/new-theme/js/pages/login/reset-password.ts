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

import LoginFormMap from '@pages/login/login-map';
import onReady from '@components/on-ready';
import ChangePasswordHandler from '@components/change-password-handler';

onReady(() => {
  const passwordHandler = new ChangePasswordHandler(LoginFormMap.passwordStrengthFeedbackContainer);
  passwordHandler.watchPasswordStrength($(LoginFormMap.resetNewPassword));
  const submitButton = document.querySelector<HTMLInputElement>(LoginFormMap.resetSubmitButton);

  if (submitButton) {
    submitButton.disabled = true;
  }

  document.addEventListener('submit', (event) => {
    const isValid = passwordHandler.isPasswordValid();

    if (submitButton) {
      submitButton.disabled = !isValid;
    }

    if (!isValid) {
      event.preventDefault();
      event.stopImmediatePropagation();
    }
  });

  const resetPasswordInput = document.querySelector<HTMLInputElement>(LoginFormMap.resetNewPassword);
  const resetPasswordConfirmationInput = document.querySelector<HTMLInputElement>(LoginFormMap.resetNewPasswordConfirmation);

  [resetPasswordInput, resetPasswordConfirmationInput].forEach((input: HTMLInputElement|null) => {
    if (input) {
      input.addEventListener('keyup', () => {
        const isValid = passwordHandler.isPasswordValid();
        const passwordsMatch = resetPasswordInput?.value === resetPasswordConfirmationInput?.value;
        const confirmFormText = resetPasswordConfirmationInput?.parentNode?.querySelector('.form-text');

        if (confirmFormText) {
          if (!passwordsMatch && resetPasswordInput?.value.length && resetPasswordConfirmationInput?.value.length) {
            confirmFormText.textContent = resetPasswordConfirmationInput?.dataset.invalidPassword ?? '';
            confirmFormText.classList.toggle('text-danger', !passwordsMatch);
            confirmFormText.classList.remove('d-none');
          } else {
            confirmFormText.classList.add('d-none');
          }
        }

        if (submitButton) {
          submitButton.disabled = !isValid || !passwordsMatch;
        }
      });
    }
  });
});
