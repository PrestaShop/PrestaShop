function tinySetup(config)
{
	if(!config)
		config = {};

	//var editor_selector = 'rte';
	//if (typeof config['editor_selector'] !== 'undefined')
		//var editor_selector = config['editor_selector'];
    if (typeof config['editor_selector'] != 'undefined')
        config['selector'] = '.'+config['editor_selector'];

//    safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview
	default_config = {
        selector: ".rte" ,
        plugins : "link image paste pagebreak table contextmenu preview filemanager table code media",
        toolbar1 : "code,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,|,blockquote,forecolor,backcolor",
        toolbar2: "pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,|,cleanup,|,media,image",
        external_filemanager_path: ad+'/../js/tiny_mce/plugins/filemanager/',
        language: iso
	}

	$.each(default_config, function(index, el)
	{
		if (config[index] === undefined )
			config[index] = el;
	});

	tinyMCE.init(config);

};
