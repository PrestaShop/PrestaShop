/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import {EventEmitter} from './event-emitter';

const {$} = window;

/**
 * This class init TinyMCE instances in the back-office. It is wildly inspired by
 * the scripts from js/admin And it actually loads TinyMCE from the js/tiny_mce
 * folder along with its modules. One improvement could be to install TinyMCE via
 * npm and fully integrate in the back-office theme.
 */
class TinyMCEEditor {
  constructor(options) {
    options = options || {};
    this.tinyMCELoaded = false;
    if (typeof options.baseAdminUrl === 'undefined') {
      if (typeof window.baseAdminDir !== 'undefined') {
        options.baseAdminUrl = window.baseAdminDir;
      } else {
        const pathParts = window.location.pathname.split('/');
        pathParts.every(pathPart => {
          if (pathPart !== '') {
            options.baseAdminUrl = `/${pathPart}/`;

            return false;
          }

          return true;
        });
      }
    }
    if (typeof options.langIsRtl === 'undefined') {
      options.langIsRtl = typeof window.lang_is_rtl !== 'undefined' ? window.lang_is_rtl === '1' : false;
    }
    this.setupTinyMCE(options);
  }

  /**
   * Initial setup which checks if the tinyMCE library is already loaded.
   *
   * @param config
   */
  setupTinyMCE(config) {
    if (typeof tinyMCE === 'undefined') {
      this.loadAndInitTinyMCE(config);
    } else {
      this.initTinyMCE(config);
    }
  }

  /**
   * Prepare the config and init all TinyMCE editors
   *
   * @param config
   */
  initTinyMCE(config) {
    config = Object.assign(
      {
        selector: '.rte',
        plugins: 'align colorpicker link image filemanager table media placeholder advlist code table autoresize',
        browser_spellcheck: true,
        toolbar1:
          'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect',
        toolbar2: '',
        external_filemanager_path: `${config.baseAdminUrl}filemanager/`,
        filemanager_title: 'File manager',
        external_plugins: {
          filemanager: `${config.baseAdminUrl}filemanager/plugin.min.js`
        },
        language: iso_user,
        content_style: config.langIsRtl ? 'body {direction:rtl;}' : '',
        skin: 'prestashop',
        menubar: false,
        statusbar: false,
        relative_urls: false,
        convert_urls: false,
        entity_encoding: 'raw',
        extended_valid_elements: 'em[class|name|id],@[role|data-*|aria-*]',
        valid_children: '+*[*]',
        valid_elements: '*[*]',
        rel_list: [{title: 'nofollow', value: 'nofollow'}],
        editor_selector: 'autoload_rte',
        init_instance_callback: () => {
          this.changeToMaterial();
        },
        setup: editor => {
          this.setupEditor(editor);
        }
      },
      config
    );

    if (typeof config.editor_selector !== 'undefined') {
      config.selector = '.' + config.editor_selector;
    }

    // Change icons in popups
    $('body').on('click', '.mce-btn, .mce-open, .mce-menu-item', () => {
      this.changeToMaterial();
    });

    tinyMCE.init(config);
    this.watchTabChanges(config);
  }

  /**
   * Setup TinyMCE editor once it has been initialized
   *
   * @param editor
   */
  setupEditor(editor) {
    editor.on('loadContent', event => {
      this.handleCounterTiny(event.target.id);
    });
    editor.on('change', event => {
      tinyMCE.triggerSave();
      this.handleCounterTiny(event.target.id);
    });
    editor.on('blur', () => {
      tinyMCE.triggerSave();
    });
  }

  /**
   * When the editor is inside a tab it can cause a bug on tab switching.
   * So we check if the editor is contained in a navigation and refresh the editor when its
   * parent tab is shown.
   *
   * @param config
   */
  watchTabChanges(config) {
    $(config.selector).each((index, textarea) => {
      const translatedField = $(textarea).closest('.translation-field');
      const tabContainer = $(textarea).closest('.translations.tabbable');

      if (translatedField.length && tabContainer.length) {
        const textareaLocale = translatedField.data('locale');
        const textareaLinkSelector = `.nav-item a[data-locale="${textareaLocale}"]`;

        $(textareaLinkSelector, tabContainer).on('shown.bs.tab', () => {
          const form = $(textarea).closest('form');
          const editor = tinyMCE.get(textarea.id);
          if (editor) {
            // Reset content to force refresh of editor
            editor.setContent(editor.getContent());
          }
        });
      }
    });
  }

  /**
   * Loads the TinyMCE javascript library and then init the editors
   *
   * @param config
   */
  loadAndInitTinyMCE(config) {
    if (this.tinyMCELoaded) {
      return;
    }

    this.tinyMCELoaded = true;
    const pathArray = config.baseAdminUrl.split('/');
    pathArray.splice(pathArray.length - 2, 2);
    const finalPath = pathArray.join('/');
    window.tinyMCEPreInit = {};
    window.tinyMCEPreInit.base = `${finalPath}/js/tiny_mce`;
    window.tinyMCEPreInit.suffix = '.min';
    $.getScript(`${finalPath}/js/tiny_mce/tinymce.min.js`, () => {
      this.setupTinyMCE(config);
    });
  }

  /**
   * Replace initial TinyMCE icons with material icons
   */
  changeToMaterial() {
    const materialIconAssoc = {
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
      'mce-i-checkbox': '<i class="mce-ico mce-i-checkbox"></i>'
    };

    $.each(materialIconAssoc, (index, value) => {
      $(`.${index}`).replaceWith(value);
    });
  }

  /**
   * Updates the characters counter
   *
   * @param id
   */
  handleCounterTiny(id) {
    const textarea = $(`#${id}`);
    const counter = textarea.attr('counter');
    const counterType = textarea.attr('counter_type');
    const max = tinyMCE.activeEditor.getBody().textContent.length;

    textarea
      .parent()
      .find('span.currentLength')
      .text(max);
    if ('recommended' !== counterType && max > counter) {
      textarea
        .parent()
        .find('span.maxLength')
        .addClass('text-danger');
    } else {
      textarea
        .parent()
        .find('span.maxLength')
        .removeClass('text-danger');
    }
  }
}

export default TinyMCEEditor;
