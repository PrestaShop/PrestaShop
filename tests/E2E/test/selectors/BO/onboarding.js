module.exports = {
  OnBoarding: {
    welcome_modal: '.onboarding-welcome',
    popup: '.onboarding-popup',
    popup_close_button: '/html/body/div[1]/div/div/div[3]/button[1]',
    stop_button: '.onboarding-button-stop',
    start_button: '//div[@class="onboarding-welcome"]//button[contains(text(), "Start")]',
    resume_button: '//*[@id="nav-sidebar"]//button[contains(@class, "resume")]',
    ready_button: '//*[@id="onboarding-welcome"]//button[contains(@class, "onboarding-button-next") and contains(text(), "I\'m ready")]',
    welcomeSteps: {
      next_button: '//div[contains(@class, "onboarding-tooltip")]//button[contains(@class, "onboarding-button-next")]',
      message_value: '//div[contains(@class, "onboarding-tooltip")]/div[@class="content"]',
      onboarding_tooltip: '//div[contains(@class, "onboarding-tooltip ")]',
      tutorial_step: '//div[@class="onboarding-advancement"]//div[@class="advancement-groups"]//div[@class="group group-%P"]//div[@class="id -done"]',
      tooltip_step: '//div[contains(@class, "onboarding-tooltip")]//span',
      understand_button: '/html/body/div/div[2]/a[1]',
      header_logo: '//*[@id="PS_LOGO-selectbutton"]'
    }
  }
};
