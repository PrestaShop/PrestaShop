function tinySetup(config)
{
	if(!config)
		config = {};

	//var editor_selector = 'rte';
	//if (typeof config['editor_selector'] !== 'undefined')
		//var editor_selector = config['editor_selector'];
    if (typeof config['editor_selector'] != 'undefined')
        config['selector'] = '.'+config['editor_selector'];

	default_config = {
        selector: ".rte" ,
        plugins : "advlist autolink link image lists charmap print preview filemanager wordcount table code media",
        toolbar1 : "styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "fontselect fontsizeselect",
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
