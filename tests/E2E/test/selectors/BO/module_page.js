module.exports = {
  ModulePage:{
  modules_subtab: '#subtab-AdminParentModulesSf',
  search_input: 'div.pstaggerAddTagWrapper > input',
  search_button: '.btn.btn-primary.pull-right.search-button',
  page_loaded: '.module-search-result-wording',
  installed_modules_tabs: '(//div[@class="page-head-tabs"]/a)[2]',
  module_number_span: '[class="module-sorting-search-wording"]',
  module_tech_name: '//div[@data-tech-name="' + module_tech_name + '" and not(@style)]',
  install_module_btn: '//div[@data-tech-name="' + module_tech_name + '" and not(@style)]//button[@data-confirm_modal="module-modal-confirm-' + module_tech_name + '-install"]',
  uninstall_module_list: '//div[@data-tech-name="' + module_tech_name + '" and not(@style)]//button[@class="btn btn-primary-outline  dropdown-toggle"]',
  uninstall_module_btn: '//div[@data-tech-name="' + module_tech_name + '" and not(@style)]//button[@class="dropdown-item module_action_menu_uninstall"]',
  modal_confirm_uninstall: '//*[@id="module-modal-confirm-' + module_tech_name + '-uninstall" and @class="modal modal-vcenter fade in"]//a[@class="btn btn-primary uppercase module_action_modal_uninstall"]'
  }
};
