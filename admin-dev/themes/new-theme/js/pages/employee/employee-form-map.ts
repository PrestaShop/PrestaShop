/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Defines all selectors that are used in employee add/edit form.
 */
export default {
  shopChoiceTree: '#employee_shop_association',
  profileSelect: '#employee_profile',
  passwordInput: '#employee_password',
  defaultPageSelect: '#employee_default_page',
  addonsConnectForm: '#addons-connect-form',
  addonsLoginButton: '#addons_login_btn',

  // selectors related to "change password" form control
  changePasswordInputsBlock: '.js-change-password-block',
  showChangePasswordBlockButton: '.js-change-password',
  hideChangePasswordBlockButton: '.js-change-password-cancel',
  oldPasswordInput: '#employee_change_password_old_password',
  newPasswordInput: '#employee_change_password_new_password_first',
  confirmNewPasswordInput: '#employee_change_password_new_password_second',
  generatedPasswordDisplayInput: '#employee_change_password_generated_password',
  generatedPasswordButton: '#employee_change_password_generate_password_button',
  passwordStrengthFeedbackContainer: '.password-strength-feedback',

  // 2FA (ps-switch radio)
  twoFactorEnabledName: 'employee[two_factor_enabled]',
  twoFactorTotpEnabledName: 'employee[two_factor_totp_enabled]',
  twoFactorEmailEnabledName: 'employee[two_factor_email_enabled]',

  // wrapper spans
  twoFactorEnabledWrapper: '#employee_two_factor_enabled',
  twoFactorTotpEnabledWrapper: '#employee_two_factor_totp_enabled',
  twoFactorEmailEnabledWrapper: '#employee_two_factor_email_enabled',
  twoFactorProvisioningUriInput: '#employee_two_factor_provisioning_uri',
  twoFactorTotQrCode: '#employee_two_factor_tot_qr_code',
  twoFactorTotCode: '#employee_two_factor_tot_verification_code',
};
