/**
 * Change default icons to marerial icons
 */
function changeToMaterial() {
  var materialIconAssoc = {
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

function tinySetup(config) {
  if (typeof tinyMCE === 'undefined') {
    setTimeout(function() {
      tinySetup(config);
    }, 100);
    return;
  }

  if (!config) {
    config = {};
  }

  if (typeof config.editor_selector != 'undefined') {
    config.selector = '.' + config.editor_selector;
  }


  var default_config = {
    selector: ".rte",
    plugins: "align colorpicker link image filemanager table media placeholder advlist code table autoresize",
    browser_spellcheck: true,
    toolbar1: "code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,table,image,media,formatselect",
    toolbar2: "",
    external_filemanager_path: baseAdminDir + "filemanager/",
    filemanager_title: "File manager",
    external_plugins: {"filemanager": baseAdminDir + "filemanager/plugin.min.js"},
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
    init_instance_callback: "changeToMaterial",
    rel_list:[
      { title: 'nofollow', value: 'nofollow' }
    ]
  };

  $.each(default_config, function (index, el) {
    if (config[index] === undefined)
      config[index] = el;
  });

  // Change icons in popups
  $('body').on('click', '.mce-btn, .mce-open, .mce-menu-item', function () {
    changeToMaterial();
  });

  tinyMCE.init(config);
}
