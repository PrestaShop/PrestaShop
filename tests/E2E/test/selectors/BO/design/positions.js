module.exports = {
  Positions: {
    search_hook: '//*[@id="hook-search"]',
    hook_element: '(//*[@id="module-positions-form"]//a[@name="displayFooter"]/..//li)[%I]',
    success_alert: '//*[@id="growls-default"]//div[@class="growl-message"]',
    footer_information: '//*[@id="footer"]//div[@class="footer-container"]/div[@class="container"]/div/div[%O][contains(@class,"block-contact")]',
    arrow_down_icon: '((//*[@id="module-positions-form"]//a[@name="displayFooter"]/..//li)[%I]//button)[2]',
    transplant_button: '//*[@id="page-header-desc-configuration-save"]',
    module_list: '//*[@id="hook_module_form"]//select[@name="id_module"]',
    transplant_to_list: '//*[@id="hook_module_form"]//select[@name="id_hook"]',
    save_button: '//*[@id="hook_module_form_submit_btn"]',
    search_module_list: '#show-modules',
    unhook_action_button: '(//*[@id="module-positions-form"]//span[contains(text(),"%B")]/../..//a)[2]',
    block_action: '//*[@id="module-positions-form"]//span[contains(text(),"%B")]/../..//div[@class="module-column-actions"]//button',
    success_panel: '//div[contains(@class, "alert-success") and @role="alert"]//div[@class="alert-text"]',
    first_category_element: '//*[@id="top-menu"]/li[1]',
    category_footer_list: '//*[@id="footer"]//ul[@class="category-sub-menu"]',
  }
};
