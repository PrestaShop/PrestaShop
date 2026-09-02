/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
