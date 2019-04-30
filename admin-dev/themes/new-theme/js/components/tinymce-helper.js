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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

class TinyMCEHelper {
  constructor(options) {
    options = options || {};
    this.tinyMCELoaded = false;
    this.setupTinyMCE(options);
  }

  setupTinyMCE(config) {
    if (typeof tinyMCE === 'undefined') {
      this.loadAndInitTinyMCE(config);
    } else {
      this.initTinyMCE(config);
    }
  }

  initTinyMCE(config) {
    config = Object.assign({
      selector: ".rte",
      plugins: "align colorpicker link image filemanager table media placeholder advlist code table autoresize",
      browser_spellcheck: true,
      toolbar1: "code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect",
      toolbar2: "",
      external_filemanager_path: baseAdminDir + "filemanager/",
      filemanager_title: "File manager",
      external_plugins: {
        "filemanager": baseAdminDir + "filemanager/plugin.min.js"
      },
      language: iso_user,
      content_style : (lang_is_rtl === '1' ? "body {direction:rtl;}" : ""),
      skin: "prestashop",
      menubar: false,
      statusbar: false,
      relative_urls: false,
      convert_urls: false,
      entity_encoding: "raw",
      extended_valid_elements: "em[class|name|id],@[role|data-*|aria-*]",
      valid_children: "+*[*]",
      valid_elements: "*[*]",
      rel_list:[
        { title: 'nofollow', value: 'nofollow' }
      ],
      editor_selector :"autoload_rte",
      init_instance_callback: () => { this.changeToMaterial(); },
      setup : (ed) => {
        ed.on('loadContent', (event) => {
          this.handleCounterTiny(tinymce.activeEditor.id);
        });
        ed.on('change', (event) => {
          tinyMCE.triggerSave();
          this.handleCounterTiny(event.target.id);
        });
        ed.on('blur', () => {
          tinyMCE.triggerSave();
        });
      }
    }, config);

    if (typeof config.editor_selector != 'undefined') {
      config.selector = '.' + config.editor_selector;
    }

    // Change icons in popups
    $('body').on('click', '.mce-btn, .mce-open, .mce-menu-item', () => { this.changeToMaterial(); });

    tinyMCE.init(config);
  }

  loadAndInitTinyMCE(config) {
    if (this.tinyMCELoaded) {
      return;
    }

    this.tinyMCELoaded = true;
    var path_array = baseAdminDir.split('/');
    path_array.splice((path_array.length - 2), 2);
    var final_path = path_array.join('/');
    window.tinyMCEPreInit = {};
    window.tinyMCEPreInit.base = final_path+'/js/tiny_mce';
    window.tinyMCEPreInit.suffix = '.min';
    $.getScript(final_path+'/js/tiny_mce/tinymce.min.js', () => {this.setupTinyMCE(config)});
  }

  changeToMaterial() {
    let materialIconAssoc = {
      'mce-i-code': '<i class="material-icons">code</i>',
      'mce-i-none': '<i class="material-icons">format_color_text</i>',
      'mce-i-bold': '<i class="material-icons">format_bold</i>',
      'mce-i-italic': '<i class="material-icons">format_italic</i>',
      'mce-i-underline': '<i class="material-icons">format_underlined</i>',
      'mce-i-strikethrough': '<i class="material-icons">format_strikethrough</i>',
      'mce-i-blockquote': '<i class="material-icons">format_quote</i>',
      'mce-i-link': '<i class="material-icons">link</i>',
      'mce-i-alignleft': '<i class="material-icons">format_align_left</i>',
      'mce-i-aligncenter': '<i class="material-icons">format_align_center</i>',
      'mce-i-alignright': '<i class="material-icons">format_align_right</i>',
      'mce-i-alignjustify': '<i class="material-icons">format_align_justify</i>',
      'mce-i-bullist': '<i class="material-icons">format_list_bulleted</i>',
      'mce-i-numlist': '<i class="material-icons">format_list_numbered</i>',
      'mce-i-image': '<i class="material-icons">image</i>',
      'mce-i-table': '<i class="material-icons">grid_on</i>',
      'mce-i-media': '<i class="material-icons">video_library</i>',
      'mce-i-browse': '<i class="material-icons">attachment</i>',
      'mce-i-checkbox': '<i class="mce-ico mce-i-checkbox"></i>',
    };

    $.each(materialIconAssoc, function (index, value) {
      $('.' + index).replaceWith(value);
    });
  }

  handleCounterTiny(id) {
    let textarea = $('#'+id);
    let counter = textarea.attr('counter');
    let counter_type = textarea.attr('counter_type');
    let max = tinyMCE.activeEditor.getBody().textContent.length;

    textarea.parent().find('span.currentLength').text(max);
    if ('recommended' !== counter_type && max > counter) {
      textarea.parent().find('span.maxLength').addClass('text-danger');
    } else {
      textarea.parent().find('span.maxLength').removeClass('text-danger');
    }
  }
}

export default TinyMCEHelper;
