module.exports = {
  ModulePage: {
    modules_subtab: '#subtab-AdminParentModulesSf',
    upload_button: '//*[@id="page-header-desc-configuration-add_module"]',
    zip_file_input: '//*[@id="importDropzone"]/input',
    installed_message: '//*[@id="importDropzone"]/div[3]/p[1]',
    close_modal_button:'//*[@id="module-modal-import-closing-cross"]',
    search_input: 'div.pstaggerAddTagWrapper > input',
    search_button: '.btn.btn-primary.pull-right.search-button',
    page_loaded: '.module-search-result-wording',
    installed_modules_tabs: '(//div[@class="page-head-tabs"]/a)[2]',
    modules_search_input:'.pstaggerAddTagInput',
    modules_search_button:'//*[@id="main-div"]/div[3]/div/div/div[2]/div/div[6]/div/div[1]/div/div[2]/button',
    action_module_built_button: '//*[@id="modules-list-container-native"]/div/div/div/div[2]/div[4]/div[2]/form/button',
    success_install_message:'//*[@id="importDropzone"]/div[3]/i',
    option_button:'//*[@id="modules-list-container-native"]/div/div/div/div[2]/div[4]/div[2]/button',
    uninstall_button:'//*[@id="modules-list-container-native"]/div/div/div/div[2]/div[4]/div[2]/div/li[1]/form/button',
    optional_button:'//*[@id="module-modal-confirm-prestafraud-uninstall"]/div/div/div[2]/div/p/label',
    uninstall_confirmation:'//*[@id="module-modal-confirm-prestafraud-uninstall"]/div/div/div[3]/a',
    built_in_module:'//*[@id="main-div"]/div[3]/div/div/div[2]/div/div[10]/span[1]'
  }
};
