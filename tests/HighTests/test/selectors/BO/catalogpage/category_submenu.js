module.exports = {
  CategorySubMenu:{
    submenu: '//*[@id="subtab-AdminCategories"]/a',
    new_category_button: '//*[@id="page-header-desc-category-new_category"]/div',
    name_input: '//*[@id="name_1"]',
    description_textarea: 'textarea#description_1',
    picture: '//*[@id="image"]',
    thumb_picture: '//*[@id="thumb"]',
    title: '//*[@id="meta_title_1"]',
    meta_description: '//*[@id="meta_description_1"]',
    keyswords: '//*[@id="fieldset_0"]/div[2]/div[10]/div/div/div[1]/div[1]/div/input',
    simplify_URL_input: '//*[@id="link_rewrite_1"]',
    save_button: '//*[@id="category_form_submit_btn"]',
    name_search_input: '//*[@id="table-category"]/thead/tr[2]/th[3]/input',
    search_button: '//*[@id="submitFilterButtoncategory"]',
    search_result: '//*[@id="table-category"]/tbody/tr/td[3]',
    update_button: '//*[@id="table-category"]/tbody/tr/td[7]/div/div',
    action_button: '//*[@id="table-category"]/tbody/tr/td[7]/div/div/button',
    delete_button: '//*[@id="table-category"]/tbody/tr/td[7]/div/div/ul/li[2]/a',
    image_link: '//*[@id="image-images-thumbnails"]/div/img',
    thumb_link: '//*[@id="thumb-images-thumbnails"]/div/img',
    second_delete_button: '//*[@id="content"]/div[6]/div/div[2]/div/form/div[5]/button[2]',
    select_category: '//*[@id="table-category"]/tbody/tr/td[1]/input',
    action_group_button: '//*[@id="form-category"]/div/div[3]/div/div/button',
    delete_action_group_button: '//*[@id="form-category"]/div/div[3]/div/div/ul/li[7]/a'
  }
};
