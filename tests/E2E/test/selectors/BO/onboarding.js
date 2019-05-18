module.exports = {
  OnBoarding: {
    welcome_modal: '.onboarding-welcome',
    popup: '.onboarding-popup',
    popup_close_button: '/html/body/div[1]/div/div/div[3]/button[1]',
    stop_button: '.onboarding-button-stop',
    start_button: '//div[@class="onboarding-welcome"]//button[contains(text(), "Start")]',
    resume_button: '//*[@id="nav-sidebar"]//button[contains(@class, "resume")] |//*[contains(@class,"nav-bar")]//button[contains(@class, "resume")]',
    ready_button: '//*[@id="onboarding-welcome"]//button[contains(@class, "onboarding-button-next") and contains(text(), "I\'m ready")]',
    later_button: '//div[@class="onboarding-welcome"]//button[contains(@class, "onboarding-button-shut-down")]',
    banktransfer_check_button: '//*[@id="main-div"]//a[contains(@href,"action/configure/%moduleTechName")]',
    banktransfer_accountowner_input: '#BANK_WIRE_OWNER',
    banktransfer_accountdetails_input: '#BANK_WIRE_DETAILS',
    banktransfer_bankaddress_input: '#BANK_WIRE_ADDRESS',
    banktransfer_save_button: '#module_form_submit_btn',
    success_alert: '//div[contains(@class,"module_confirmation")]',
    install_paypal_button : '//*[@id="main-div"]//a[contains(@href,"module_name=paypal")]',
    paypal_conf_page : '//*[@id="paypal_conf"]',
    edit_carrier_button: '//*[@id="table-carrier"]//tr[@id="tr_2_1_0"]//td//a[contains(@class,"edit")]',
    install_chronopost_button: '//div[contains(@class,"modules_list_container_tab")]//td//a[contains(@href,"install=chronopost")]',
    over_to_you_modal: '#onboarding-welcome',
    onboarding_navbar: '.onboarding-navbar.displayed',
    onboarding_navbar_stop_button: 'a.onboarding-button-stop',
    welcomeSteps: {
      continue_button: '//*[@id="main-div"]//button[contains(@class,"onboarding-button-next")]',
      next_button: '//div[contains(@class, "onboarding-tooltip")]//button[contains(@class, "onboarding-button-next")]',
      message_value: '//div[contains(@class, "onboarding-tooltip")]/div[@class="content"]',
      onboarding_tooltip: '//div[contains(@class, "onboarding-tooltip ")]',
      tutorial_step: '//div[@class="onboarding-advancement"]//div[@class="advancement-groups"]//div[@class="group group-%P"]//div[@class="id -done"]',
      tooltip_step: '//div[contains(@class, "onboarding-tooltip")]//span',
      understand_button: '/html/body/div/div[2]/a[1]',
      header_logo: '//*[@id="PS_LOGO-selectbutton"]',
      starter_guide_button: '//*[@id="onboarding-welcome"]//div[@class="starter-guide"]',
      forum_button: '//*[@id="onboarding-welcome"]//div[@class="forum"]',
      training_button: '//*[@id="onboarding-welcome"]//div[@class="training"]',
      video_tutorial_button: '//*[@id="onboarding-welcome"]//div[@class="video-tutorial"]',
      discover_button: '(//*[@id="main-div"]//div[contains(@class,"addons-theme-footer-container")]//p[contains(@class,"addons-theme-discover")])[1]'
    },
    externals: {
      documentation_title: '//span[@class="plugin_pagetree_children_span"]//a[contains (@href, "English+documentation")] ',
      discover_training_button: '(//a[contains(@href, "training/list")])[1]',
      forum_title: '//*[@id="ipsLayout_mainArea"]//h1[@class="ipsType_pageTitle"]',
      youtube_channel_title: '//*[@id="channel-title"]'
    }
  }
};
