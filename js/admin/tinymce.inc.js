function getTinyMaterialIconsAssoc()
{
    return {
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
        'mce-i-bullist': '<i class="material-icons">format_list_bulleted</i>',
        'mce-i-numlist': '<i class="material-icons">format_list_numbered</i>',
        'mce-i-image': '<i class="material-icons">image</i>'
    }
}

function tinySetup(config)
{
	if(!config)
		config = {};

	//var editor_selector = 'rte';

	if (typeof config.editor_selector != 'undefined')
		config.selector = '.'+config.editor_selector;

	default_config = {
		selector: ".rte" ,
		plugins : "colorpicker link image filemanager table media placeholder",
		browser_spellcheck : true,
		toolbar1 : "colorpicker,bold,italic,underline,strikethrough,blockquote,link,alignleft,aligncenter,alignright,alignfull,bullist,numlist,image",
		toolbar2: "",
		external_filemanager_path: baseAdminDir+"filemanager/",
		filemanager_title: "File manager" ,
		external_plugins: { "filemanager" : baseAdminDir+"filemanager/plugin.min.js"},
		language: iso_user,
		skin: "prestashop",
        menubar:false,
		statusbar: false,
		relative_urls : false,
		convert_urls: false,
		entity_encoding: "raw",
		extended_valid_elements : "em[class|name|id]",
		valid_children : "+*[*]",
		valid_elements:"*[*]",

        init_instance_callback : function(editor) {
            var editorDom = editor.editorContainer;

            if (editorDom) {
                var jQButtonSelector = '.mce-toolbar:not(.mce-menubar) > .mce-container-body > .mce-container > div > .mce-widget > button > i';
                var editorButtonsIcons = $(editorDom).find(jQButtonSelector);

                if (editorButtonsIcons) {

                    var materialIconAssoc = getTinyMaterialIconsAssoc();

                    $.each(editorButtonsIcons, function(index, value){
                        // Clean extra class on object to keep the only one we need
                        $(this).removeClass('mce-ico');
                        var tinyIcoClass = $(this).attr('class');

                        if (typeof materialIconAssoc[tinyIcoClass] != 'undefined') {
                            $(this).replaceWith(materialIconAssoc[tinyIcoClass]);
                        }

                    });

                }
            }
        }
	};

	$.each(default_config, function(index, el)
	{
		if (config[index] === undefined )
			config[index] = el;
	});

	tinyMCE.init(config);
}
