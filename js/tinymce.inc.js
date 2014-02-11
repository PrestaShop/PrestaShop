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
		plugins : "link image paste pagebreak table contextmenu preview filemanager table code media autoresize",
		toolbar1 : "code,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,|,blockquote,forecolor,backcolor,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,|,cleanup,|,media,image",
		toolbar2: "",
		external_filemanager_path: ad+"/filemanager/",
		filemanager_title: "File manager" ,
		external_plugins: { "filemanager" : ad+"/filemanager/plugin.min.js"},
		language: iso,
		skin: "prestashop",
		resize: false
	}

	$.each(default_config, function(index, el)
	{
		if (config[index] === undefined )
			config[index] = el;
	});

	tinyMCE.init(config);

};