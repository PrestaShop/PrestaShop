/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
module.exports = {
  CategorySubMenu: {
    submenu: '//*[@id="subtab-AdminCategories"]/a',
    new_category_button: '//*[@id="page-header-desc-category-new_category"]/div',
    name_input: '//*[@id="name_1"]',
    description_textarea: '//*[@id="category_form"]//div[@class="mce-tinymce mce-container mce-panel"]',
    picture: '//*[@id="image"]',
    thumb_picture: '//*[@id="thumb"]',
    thumb_menu_picture: '//*[@id="thumbnail"]',
    upload_files_button: '//*[@id="thumbnail-upload-button"]',
    thumbnail_success_alert: '//*[@id="thumbnail-success"]',
    thumbnail_menu_img: '//*[@id="thumbnail-images-thumbnails"]//img',
    title: '//*[@id="meta_title_1"]',
    meta_description: '//*[@id="meta_description_1"]',
    keyswords: '(//*[@id="fieldset_0"]//input[@placeholder="Add tag"])[1]',
    simplify_URL_input: '//*[@id="link_rewrite_1"]',
    save_button: '//*[@id="category_form_submit_btn"]',
    reset_button: '//*[@id="table-category"]//th[7]//button[contains(@name, "Reset")]',
    search_input: '//*[@id="table-category"]//input[@name="categoryFilter_name"]',
    search_button: '//*[@id="submitFilterButtoncategory"]',
    search_result: '//*[@id="table-category"]//td[3]',
    update_button: '//*[@id="table-category"]//td[7]/div/div',
    action_button: '//*[@id="table-category"]//td[7]//button[contains(@class, "dropdown-toggle")]',
    delete_button: '//*[@id="table-category"]//td[7]//a[@title="Delete"]',
    view_button: '//*[@id="table-category"]//td[7]//a[@title="View"]',
    image_link: '//*[@id="image-images-thumbnails"]/div/img',
    thumb_link: '//*[@id="thumb-images-thumbnails"]/div/img',
    second_delete_button: '//*[@id="content"]//form//button[not(@name)]',
    select_category: '//*[@id="table-category"]/tbody/tr/td[1]/input',
    action_group_button: '//*[@id="form-category"]//div[contains(@class, "bulk-actions")]/button[contains(@class, "dropdown-toggle")]',
    delete_action_group_button: '//*[@id="form-category"]//a[contains(@onclick, "Delete selected")]',
    category_number_span: '//*[@id="form-category"]//span[@class="badge"]',
    category_description: '//*[@id="category_form"]//div[@class="mce-tinymce mce-container mce-panel"]',
    description: '//*[@id="table-category"]//td[4]',
    delete_tag_button: '//div[@class="tagify-container"]//span[%POS]/a',
    mode_link_disable_radio: '//*[@id="deleteMode_linkanddisable"]',
    mode_link_radio: '//*[@id="deleteMode_link"]',
    mode_delete_radio: '//*[@id="deleteMode_delete"]',
    expand_all_button: '#expand-all-categories-tree',
    parent_category: '(//*[@id="categories-tree"]//span[contains(.,"%NAME")]//input)[1]',
    search_no_results: '//*[@id="table-category"]//td[@class="list-empty"]'
  }
};
