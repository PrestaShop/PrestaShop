module.exports = {
  accountPage: {
    create_button: '[data-link-action="display-register-form"]',
    firstname_input: '[name="firstname"]',
    lastname_input: '[name="lastname"]',
    email_input: '[name="email"]',
    password_input: '[name="password"]',
    save_account_button: '[data-link-action="save-customer"]',
    radio_button_gender: '(//*[@id="customer-form"]//input[contains(@name,"id_gender")])[2]',
    account_link: '//*[@id="_desktop_user_info"]//a[@class="account"]',
    add_first_address: '#address-link',
    adr_address: '//*[@id="content"]//input[@name="address1"]',
    adr_postcode: '//*[@id="content"]//input[@name="postcode"]',
    adr_city: '//*[@id="content"]//input[@name="city"]',
    adr_save: '//*[@id="content"]//footer/button',
    success_alert: '[data-alert="success"]',
    adr_update: '[data-link-action="edit-address"]',
    //------------------ connect with existing account from checkout ---------------//
    sign_tab: '//*[@id="checkout-personal-information-step"]//a[contains(text(), "Sign in")]',
    signin_email_input: '//*[@id="login-form"]//input[@name="email"]',
    signin_password_input: '//*[@id="login-form"]//input[@name="password"]',
    continue_button: '//*[@id="login-form"]//button[contains(@class, "continue")]',
    //---------------------- create account from checkout -------------------------//
    new_customer_btn: '[data-link-action="register-new-customer"]',
    new_address_btn: '[name="confirm-addresses"]',
    new_email_input: '//*[@id="customer-form"]//input[@name="email"]',
    new_password_input: '//*[@id="customer-form"]//input[@name="password"]',
    shipping_continue_btn: '//*[@id="js-delivery"]/button[@name="confirmDeliveryOption"]',
    pay_by_check: '//*[@id="payment-option-1"]',
    terms_of_service: '//*[@id="conditions_to_approve[terms-and-conditions]"]',
    order_button: '//*[@id="payment-confirmation"]//button',
    confirmed_order_message: '//*[@id="content-hook_order_confirmation"]//h3[contains(@class, "card-title")]',
    email_sent_message: '//*[@id="content-hook_order_confirmation"]//p',
    notification_save_adr: '//*[@id="notifications"]//li'
  }
};