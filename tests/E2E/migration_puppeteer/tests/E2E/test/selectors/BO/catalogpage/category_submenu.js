module.exports = {
  CategorySubMenu: {
    submenu: '#subtab-AdminCategories a',
    new_category_button: "#page-header-desc-category-new_category",
    name_input: '#name_1 ',
    description_textarea: '#category_form #mce_31',
//    description_textarea: '#category_form  div.mce-tinymce mce-container mce-panel ',
    picture: '#image ',
    thumb_picture: '#thumb ',
    thumb_menu_picture: '#thumbnail ',
    upload_files_button: '#thumbnail-upload-button ',
    thumbnail_success_alert: '#thumbnail-success ',
    thumbnail_menu_img: '#thumbnail-images-thumbnails img',
    title: '#meta_title_1 ',
    meta_description: '#meta_description_1 ',
    keyswords: '#fieldset_0 input[placeholder="Add tag"]:nth-child(%POS)',
    simplify_URL_input: '#link_rewrite_1 ',
    save_button: '#category_form_submit_btn ',
    reset_button: "#table-category button[name='submitResetcategory']",
    search_input: '#table-category  input[name="categoryFilter_name"]',
    search_button: '#submitFilterButtoncategory ',
    search_result: '#table-category td[3]',
    update_button: '#table-category td:nth-child(7) .edit',
    action_button: '#table-category td:nth-child(7) button.dropdown-toggle',
    delete_button: '#table-category td:nth-child(7) a.delete ',
    view_button: '#table-category  td:nth-child(7) a[title="View"] ',
    image_link: '#image-images-thumbnails div img',
    thumb_link: '#thumb-images-thumbnails div img',
    second_delete_button: '#content form button:not([name="cancel"])',
    select_category: '#table-category tbody tr td:nth-child(1) input',
    action_group_button: '#form-category  div .bulk-actions button.dropdown-toggle',
    delete_action_group_button: '#form-category  a[onclick*="Delete selected"]',
    category_number_span: '#form-category span.badge ',
    category_description: '#category_form div.mce-tinymce mce-container mce-panel ',
    description: '#table-category td:nth-child(4)',
    delete_tag_button: 'div .tagify-container span:nth-child(%POS) a',
    mode_link_disable_radio: '#deleteMode_linkanddisable ',
    mode_link_radio: '#deleteMode_link ',
    mode_delete_radio: '#deleteMode_delete ',
    expand_all_button: '#expand-all-categories-tree',
    parent_category: '(#categories-tree span[contains(.,"%NAME")] input)[1]',
    search_no_results: '#table-category td.list-empty ',
    category_name: '#table-category tr:nth-child(%ID) td:nth-child(3)',
    category_view_button: '#table-category tr:nth-child(%ID) td:nth-child(7) a[title="View"] ',
    category_id_column:'#table-category tr:nth-child(%ID) td:nth-child(2)',

  }
};
