function tinySetup(config)
{
	if(!config)
		config = {};

	default_config = {
		mode : "specific_textareas",
		theme : "advanced",
		skin:"cirkuit",
		editor_selector : "rte",
		editor_deselector : "noEditor",
		plugins : "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview",
		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor, media, fullscreen",
	//	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,ltr,rtl,|",
	//	theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,pagebreak",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",	
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		content_css : pathCSS+"global.css",
		document_base_url : ad,
		width: "600",
		height: "auto",
		font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
		elements : "nourlconvert,ajaxfilemanager",
		file_browser_callback : "ajaxfilemanager",
		entity_encoding: "raw",
		convert_urls : false,
		language : iso
	}

	$.each(default_config, function(index, el)
	{
		if (config[index] === undefined )
			config[index] = el;
	});
	tinyMCE.init(config);
};


	function ajaxfilemanager(field_name, url, type, win) {
		var ajaxfilemanagerurl = ad+"/ajaxfilemanager/ajaxfilemanager.php";
		switch (type) {
			case "image":
				break;
			case "media":
				break;
			case "flash": 
				break;
			case "file":
				break;
			default:
				return false;
	}
    tinyMCE.activeEditor.windowManager.open({
        url: ajaxfilemanagerurl,
        width: 782,
        height: 440,
        inline : "yes",
        close_previous : "no"
    },{
        window : win,
        input : field_name
    });
}
