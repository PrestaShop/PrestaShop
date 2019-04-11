module.exports = {
  OnBoarding: {
    welcome_modal: '.onboarding-welcome',
    popup: '.onboarding-popup',
    popup_close_button: 'body button.onboarding-button-shut-down',
    stop_button: 'a [class*=onboarding-button-stop]',
    start_button: ' div.onboarding-welcome button[contains(text(), "Start")]',
    resume_button: '#nav-sidebar button.resume | *.nav-bar button.resume',
    ready_button: '#onboarding-welcome button.onboarding-button-next") and contains(text(), "I\'m ready")',
    later_button: ' div.onboarding-welcome button.onboarding-button-shut-down',
    banktransfer_check_button: '#main-div a [contains(href,"action configure %moduleTechName")]',
    banktransfer_accountowner_input: '#BANK_WIRE_OWNER',
    banktransfer_accountdetails_input: '#BANK_WIRE_DETAILS',
    banktransfer_bankaddress_input: '#BANK_WIRE_ADDRESS',
    banktransfer_save_button: '#module_form_submit_btn',
    success_alert: ' div.module_confirmation',
    install_paypal_button : '#main-div a [contains(href,"module_name=paypal&anchor=Paypal")]',
    paypal_conf_page : '#paypal_conf ',
    edit_carrier_button: '#table-carrier tr[id="tr_2_1_0 td a .edit")]',
    install_chronopost_button: ' div.modules_list_container_tab td a [contains(href,"install=chronopost")]',
    over_to_you_modal: '#onboarding-welcome',
    welcomeSteps: {
      continue_button: '#main-div button.onboarding-button-next',
      next_button: ' div.onboarding-tooltip button.onboarding-button-next',
      message_value: ' div.onboarding-tooltip div.content ',
      onboarding_tooltip: ' div.onboarding-tooltip ',
      tutorial_step: ' div.onboarding-advancement div.advancement-groups div.group group-%P div.id -done ',
      tooltip_step: ' div.onboarding-tooltip span',
      understand_button: ' html body div div:nth-child(2) a:nth-child(1)',
      header_logo: '#PS_LOGO-selectbutton ',
      starter_guide_button: '#onboarding-welcome div.starter-guide ',
      forum_button: '#onboarding-welcome div.forum ',
      training_button: '#onboarding-welcome div.training ',
      video_tutorial_button: '#onboarding-welcome div.video-tutorial ',
      discover_button: '#main-div div.addons-theme-footer-container p.addons-theme-discover):nth-child(1)'
    },
    externals: {
      documentation_title: ' span.plugin_pagetree_children_span a [contains (href, "English+documentation")] ',
      discover_training_button: '( a [contains(href, "training list")]):nth-child(1)',
      forum_title: '#ipsLayout_mainArea h1.ipsType_pageTitle ',
      youtube_channel_title: '#channel-title '
    }
  }
};
