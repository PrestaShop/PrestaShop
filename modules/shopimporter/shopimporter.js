var shopImporter = {
	token: globalAjaxShopImporterToken,
	moduleName: $('#import_module_name').val(),
	server: $('#server').val(),
	user: $('#user').val(),
	password: $('#password').val(),
	database: $('#database').val(),
	prefix: $('#prefix').val(),
	url: $('#url').val(),
	loginws: $('#loginws').val(),
	apikey: $('#apikey').val(),
	specificOptions : '',
	imagesOptions : '',
	output : 1,
	hasErrors : 0,
	limit: 0,
	nbr_import: parseInt($('#nbr_import').val()),
	idMethod: 0,
	nbrMethod: 0,
	save : 0,
	srcError : '../modules/shopimporter/img/error.png',
	srcConf : '../modules/shopimporter/img/ok.png',
	srcImport : '../modules/shopimporter/img/ajax-loader.gif',
	srcWarn : '../modules/shopimporter/img/warn.png',
	srcDelete : '../modules/shopimporter/img/delete.gif',
	
	
	syncLangWS : function (onComplete)
	{
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&syncLangWS&getMethod=getLangagues&token='+this.token+'&className=Language&moduleName='+this.moduleName+'&url='+this.url+'&loginws='+this.loginws+'&apikey='+this.apikey+'&nbr_import='+this.nbr_import ,
	       success: function(jsonData)
	       {
				if (jsonData.hasError)
	    		{
	    			$('#steps').html('<div id="lang_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">'+jsonData.error+'</div>');
		    		$('#lang_feedback').fadeIn('slow');
		    		onComplete(false);
	    		}
	    		else
	    			onComplete(true);
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
	       		$('#technical_error_feedback').fadeIn('slow');
	       		onComplete(false);
	       }
	   });
	 
	},
	syncLang : function (onComplete)
	{
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&syncLang&getMethod=getLangagues&token='+this.token+'&className=Language&moduleName='+this.moduleName+'&server='+this.server+'&user='+this.user+'&password='+this.password+'&database='+this.database+'&prefix='+prefix+this.specificOptions+'&nbr_import='+this.nbr_import ,
	       success: function(jsonData)
	       {
				if (jsonData.hasError)
	    		{
	    			$('#steps').html('<div id="lang_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">'+jsonData.error+'</div>');
		    		$('#lang_feedback').fadeIn('slow');
		    		onComplete(false);
	    		}
	    		else
	    			onComplete(true);
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
	       		$('#technical_error_feedback').fadeIn('slow');
	       		onComplete(false);
	       }
	   });
	 
	},
	syncCurrencyWS : function (onComplete)
	{
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&token='+this.token+'&syncCurrencyWS&getMethod=getCurrencies&className=Currency&moduleName='+this.moduleName+'&url='+this.url+'&loginws='+this.loginws+'&apikey='+this.apikey+'&nbr_import='+this.nbr_import ,
	       success: function(jsonData)
	       {
				if (jsonData.hasError)
	    		{
	    			$('#steps').html('<div id=\'currency_feedback\' style=\'display:none;\' class=\'error\'><img src=\''+shopImporter.srcError+'\'>'+jsonData.error+'</div>');
		    		$('#currency_feedback').fadeIn('slow');
					onComplete(false);
	    		}
	    		else
	    			onComplete(true);
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
	       		$('#technical_error_feedback').fadeIn('slow');
	       		onComplete(false);
	       }
	   });
	},
	syncCurrency : function (onComplete)
	{
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&token='+this.token+'&syncCurrency&getMethod=getCurrencies&className=Currency&moduleName='+this.moduleName+'&server='+this.server+'&user='+this.user+'&password='+this.password+'&database='+this.database+'&prefix='+prefix+this.specificOptions+'&nbr_import='+this.nbr_import ,
	       success: function(jsonData)
	       {
				if (jsonData.hasError)
	    		{
	    			$('#steps').html('<div id="currency_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">'+jsonData.error+'</div>');
		    		$('#currency_feedback').fadeIn('slow');
					onComplete(false);
	    		}
	    		else
	    			onComplete(true);
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
	       		$('#technical_error_feedback').fadeIn('slow');
	       		onComplete(false);
	       }
	   });
	 
	},
	checkAndSaveConfigWSDL : function (onComplete)
	{
		$('#checkAndSaveConfig').fadeOut('slow');
       	$('#steps').html($('#steps').html()+'<div id=\'database_feedback\' style=\'display:none;\' class=\'conf\'><img src=\''+shopImporter.srcConf+'\'>'+wsOk+'</div>');
    	$('#steps').html($('#steps').html()+'<input style=\'display:none\' type=\'submit\' name=\'next\' id=\'next\' class=\'button\' value="'+testImport+'">');
    	$('#next').fadeIn('slow', function () { 
	    	$('#next').unbind('click').click(function(){
				$('#next').fadeOut('fast', function() {
					shopImporter.nbrMethod = conf.length;
					shopImporter.getDatasWS(conf[shopImporter.idMethod]);
				});
				return false;
			});
		});
    	$('#database_feedback').fadeIn('slow');
	 
	},
	checkAndSaveConfigWS : function (save)
	{
		//sync languages and currency
		this.syncLangWS(function(isOk) {
			if (isOk)
			{
				shopImporter.syncCurrencyWS(function(isOk) {
							       	if ($('#technical_error_feedback').length)
							       		$('#technical_error_feedback').fadeIn('slow');
							       
							       
								       	$('#checkAndSaveConfig').fadeOut('slow');
				       	$('#steps').html($('#steps').html()+'<div id=\'database_feedback\' style=\'display:none;\' class=\'conf\'><img src=\''+shopImporter.srcConf+'\'>'+databaseOk+'</div>');
				    	$('#steps').html($('#steps').html()+'<input style=\'display:none\' type=\'submit\' name=\'next\' id=\'next\' class=\'button\' value="'+testImport+'">');
								    	$('#database_feedback').fadeIn('slow', function() {
						    			if (save)
								    	{
								    		shopImporter.idMethod = 0;
								    		shopImporter.limit = 0;
								    		shopImporter.nbrMethod = conf.length;
								    		$('.truncateTable:checked').each(function (){ 
								    			shopImporter.truncatTable(this.id, 'add'); 
								    		});
											
											if($('#truncat_feedback').length != 0)
												$('#truncat_feedback').removeClass('import').addClass('conf');
											
								    		shopImporter.getDatasWS(conf[shopImporter.idMethod]);
								    	}
								    	else
								    	{
									    	$('#next').fadeIn('slow', function () { 
										    	$('#next').unbind('click').click(function(){
													$('#next').fadeOut('fast', function() {
														shopImporter.nbrMethod = conf.length;
														shopImporter.getDatasWS(conf[shopImporter.idMethod]);
													});
													
												});
											});
										}
							    	});		    	
				});
			}
		});			    
	},
	checkAndSaveConfig : function (save)
	{
		//sync languages and currency
		this.syncLang(function(isOk) {
			if (isOk)
			{
				shopImporter.syncCurrency(function(isOk) {
					if (isOk)
					{
						$.ajax({
						       type: 'GET',
						       url: '../modules/shopimporter/ajax.php',
						       async: true,
						       cache: false,
						       dataType : "json",
						       data: 'ajax=true&token='+this.token+'&checkAndSaveConfig&moduleName='+shopImporter.moduleName+'&server='+shopImporter.server+'&user='+shopImporter.user+'&password='+shopImporter.password+'&database='+shopImporter.database+'&prefix='+prefix+shopImporter.specificOptions+'&nbr_import='+shopImporter.nbr_import ,
						       success: function(jsonData)
						       {
							       	if ($('#technical_error_feedback').length)
							       		$('#technical_error_feedback').fadeIn('slow');
							       
							       	if (!jsonData.hasError)
						    		{
								       	$('#checkAndSaveConfig').fadeOut('slow');
								       	$('#steps').html($('#steps').html()+'<div id="database_feedback" style="display:none;" class="conf"><img src="'+shopImporter.srcConf+'">'+databaseOk+'</div>');
								    	$('#steps').html($('#steps').html()+'<input style="display:none" type="submit" name="next" id="next" class="button" value="'+testImport+'">');
								    	$('#database_feedback').fadeIn('slow', function() {
						    			if (save)
								    	{
								    		shopImporter.idMethod = 0;
								    		shopImporter.limit = 0;
								    		shopImporter.nbrMethod = conf.length;
								    		$('.truncateTable:checked').each(function (){ 
								    			shopImporter.truncatTable(this.id, 'add'); 
								    		});
											
											if($('#truncat_feedback').length != 0)
												$('#truncat_feedback').removeClass('import').addClass('conf');
											
								    		shopImporter.getDatas(conf[shopImporter.idMethod]);
								    	}
								    	else
								    	{
									    	$('#next').fadeIn('slow', function () { 
										    	$('#next').unbind('click').click(function(){
													$('#next').fadeOut('fast', function() {
														shopImporter.nbrMethod = conf.length;
														shopImporter.getDatas(conf[shopImporter.idMethod]);
													});
													return false;
												});
											});
										}
							    	});		    	
							    }
							    else
							    {
							    	$('#steps').html('<div id="database_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">'+jsonData.error+'</div>');
							    	$('#database_feedback').fadeIn('slow');
							    }
						       },
						      error: function(XMLHttpRequest, textStatus, errorThrown) 
						       {
					       		$('#steps').html($('#steps').html()+'<div id=\'technical_error_feedback\' style=\'display:none;\' class=\'error\'><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
						       		$('#technical_error_feedback').fadeIn('slow');
						       }
						   });
					}
				});
			}
		});
	},
	
	getDatasWS : function (methodName)
	{		
		//check if method have to be call
		if (shopImporter.idMethod >= shopImporter.nbrMethod)
			shopImporter.displayEnd(false);
		else if ($('input[name='+methodName[0]+']:radio:checked').val() == 0)
		{
			shopImporter.idMethod ++;
			shopImporter.getDatasWS(conf[shopImporter.idMethod]);
			return;
		}
		if (typeof(methodName) != 'undefined')
		{
			
			$('#steps').html($('#steps').html()+'<div id=\'ok_feedback_'+methodName[0]+'\' style=\'display:none;\' class="import"><img src=\''+this.srcImport+'\'>'+methodName[1]+'<span id=\'display_error_'+methodName[0]+'\' style=\'display:none\'><span><div id=\'feedback_'+methodName[0]+'_errors_list\'></div></div>');
			$('#ok_feedback_'+methodName[0]).css('display', '');
		
		$('#checkAndSaveConfig').fadeIn('slow');
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: true,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&token='+this.token+'&getDataWS&className='+methodName[2]+'&getMethod='+methodName[0]+'&moduleName='+this.moduleName+'&url='+this.url+'&loginws='+this.loginws+'&apikey='+this.apikey+'&limit='+this.limit+'&nbr_import='+this.nbr_import+'&save='+this.save+'&errors='+this.errors+'&hasErrors='+this.hasErrors+this.specificOptions+this.imagesOptions ,
	       success: function(jsonData)
	       {	
		       	var jsonError;
		       	if (jsonData.hasError)
	    		{
					jsonError = '';
					if (jsonData.error == 'not_exist')
					{
						$('#ok_feedback_'+methodName[0]).removeClass('conf').addClass(function() { 
							$('#ok_feedback_'+methodName[0]).html('<img src="'+shopImporter.srcWarn+'">'+methodName[1]+' '+notExist);
							return 'warn';
						});
					}
					else
					{
						for (i=0;i<jsonData.error.length;i++)
							jsonError = jsonError+'<li>Id : '+jsonData.error[i]+'</li>';
									
						if ($('#display_error_'+methodName[0]+'_link').length == 0)
						{
							$('#ok_feedback_'+methodName[0]).html($('#ok_feedback_'+methodName[0]).html()+'<span id="display_error_'+methodName[0]+'" style="float:right;"><a id="display_error_'+methodName[0]+'_link" class="display_error_link" rel="'+methodName[0]+'" href="#" onclick="enableShowErrors(\''+methodName[0]+'\'); return false;">'+showErrors+'(<span id="nbr_errors_'+methodName[0]+'">'+jsonData.error.length+'</span>)'+'</a></span><div style="display:none;" id="feedback_'+methodName[0]+'_errors_list"><ul>'+jsonError+'</ul></div>');
						}
						else
						{
							var nbrErrors = $('#nbr_errors_'+methodName[0]).html();
							var newNbrError = parseInt(jsonData.error.length) + parseInt(nbrErrors);
							$('#nbr_errors_'+methodName[0]).html(newNbrError);
							$('#feedback_'+methodName[0]+'_errors_list > ul').html($('#feedback_'+methodName[0]+'_errors_list > ul').html() + jsonError);
						}
					}
					
					if (jsonData.datas.length != parseInt(shopImporter.nbr_import))
					{
						if ($('#display_error_'+methodName[0]+'_link').length != 0)
							$('#ok_feedback_'+methodName[0]).removeClass('import').addClass( function() {
								$('#ok_feedback_'+methodName[0]+' >img:first').attr('src', shopImporter.srcError);
								return 'error';
							});
						shopImporter.idMethod ++;
						shopImporter.limit = 0;
					}
					else
						shopImporter.limit += parseInt(shopImporter.nbr_import);
					if ((shopImporter.idMethod < shopImporter.nbrMethod))
						shopImporter.getDatasWS(conf[shopImporter.idMethod]);
					else
						shopImporter.displayEnd(false);
	    		}
	    		else
	    		{
    				if (jsonData.datas.length != parseInt(shopImporter.nbr_import))
    				{
						if ($('#display_error_'+methodName[0]+'_link').length != 0)
						{
							$('#ok_feedback_'+methodName[0]).removeClass('import').addClass( function() {
								$('#ok_feedback_'+methodName[0]+' >img:first').attr('src', shopImporter.srcError);
								return 'error';
							});
						}else
						{
							$('#ok_feedback_'+methodName[0]).removeClass('import').addClass('conf');
							$('#ok_feedback_'+methodName[0]+'>img:first').attr('src', shopImporter.srcConf);
						}
						shopImporter.idMethod ++;
						shopImporter.limit = 0;
					
						shopImporter.getDatasWS(conf[shopImporter.idMethod]);
					}
					else
					{
						if (shopImporter.idMethod < shopImporter.nbrMethod)
						{
							shopImporter.limit += parseInt(shopImporter.nbr_import);
							shopImporter.getDatasWS(conf[shopImporter.idMethod]);
						}
						else	
							shopImporter.displayEnd(true);
					}	
				}
	       },
	       error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#technical_error_feedback').fadeIn('slow');
	       		$('#checkAndSaveConfig').fadeIn('slow');
	       }
	   });
	   }
	},
	getDatas : function (methodName)
	{		
		//check if method have to be call
		if (shopImporter.idMethod >= shopImporter.nbrMethod)
			shopImporter.displayEnd(false);
		else if ($('input[name='+methodName[0]+']:radio:checked').val() == 0)
		{
			shopImporter.idMethod ++;
			shopImporter.getDatas(conf[shopImporter.idMethod]);
			return;
		}
		if (typeof(methodName) != 'undefined')
		{
			
			$('#steps').html($('#steps').html()+'<div id="ok_feedback_'+methodName[0]+'" style="display:none;" class="import"><img src="'+this.srcImport+'">'+methodName[1]+'<span id="display_error_'+methodName[0]+'" style="display:none"><span><div id="feedback_'+methodName[0]+'_errors_list"></div></div>');
			$('#ok_feedback_'+methodName[0]).css('display', '');
		
		$('#checkAndSaveConfig').fadeIn('slow');
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: true,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&token='+this.token+'&getData&className='+methodName[2]+'&getMethod='+methodName[0]+'&moduleName='+this.moduleName+'&server='+this.server+'&user='+this.user+'&password='+this.password+'&database='+this.database+'&prefix='+prefix+'&limit='+this.limit+'&nbr_import='+this.nbr_import+'&save='+this.save+'&errors='+this.errors+'&hasErrors='+this.hasErrors+this.specificOptions+this.imagesOptions ,
	       success: function(jsonData)
	       {	
		       	var jsonError;
		       	if (jsonData.hasError)
	    		{
					jsonError = '';
					if (jsonData.error == 'not_exist')
					{
						$('#ok_feedback_'+methodName[0]).removeClass('conf').addClass(function() { 
							$('#ok_feedback_'+methodName[0]).html('<img src="'+shopImporter.srcWarn+'">'+methodName[1]+' '+notExist);
							return 'warn';
						});
					}
					else
					{
						for (i=0;i<jsonData.error.length;i++)
							jsonError = jsonError+'<li>Id : '+jsonData.error[i]+'</li>';
									
						if ($('#display_error_'+methodName[0]+'_link').length == 0)
						{
							$('#ok_feedback_'+methodName[0]).html($('#ok_feedback_'+methodName[0]).html()+'<span id="display_error_'+methodName[0]+'" style="float:right;"><a id="display_error_'+methodName[0]+'_link" class="display_error_link" rel="'+methodName[0]+'" href="#" onclick="enableShowErrors(\''+methodName[0]+'\'); return false;">'+showErrors+'(<span id="nbr_errors_'+methodName[0]+'">'+jsonData.error.length+'</span>)'+'</a></span><div style="display:none;" id="feedback_'+methodName[0]+'_errors_list"><ul>'+jsonError+'</ul></div>');
						}
						else
						{
							var nbrErrors = $('#nbr_errors_'+methodName[0]).html();
							var newNbrError = parseInt(jsonData.error.length) + parseInt(nbrErrors);
							$('#nbr_errors_'+methodName[0]).html(newNbrError);
							$('#feedback_'+methodName[0]+'_errors_list > ul').html($('#feedback_'+methodName[0]+'_errors_list > ul').html() + jsonError);
						}
					}
					
					if (jsonData.datas.length != parseInt(shopImporter.nbr_import))
					{
						if ($('#display_error_'+methodName[0]+'_link').length != 0)
							$('#ok_feedback_'+methodName[0]).removeClass('import').addClass( function() {
								$('#ok_feedback_'+methodName[0]+' >img:first').attr('src', shopImporter.srcError);
								return 'error';
							});
						shopImporter.idMethod ++;
						shopImporter.limit = 0;
					}
					else
						shopImporter.limit += parseInt(shopImporter.nbr_import);
					if ((shopImporter.idMethod < shopImporter.nbrMethod))
						shopImporter.getDatas(conf[shopImporter.idMethod]);
					else
						shopImporter.displayEnd(false);
	    		}
	    		else
	    		{
    				if (jsonData.datas.length != parseInt(shopImporter.nbr_import))
    				{
						if ($('#display_error_'+methodName[0]+'_link').length != 0)
						{
							$('#ok_feedback_'+methodName[0]).removeClass('import').addClass( function() {
								$('#ok_feedback_'+methodName[0]+' >img:first').attr('src', shopImporter.srcError);
								return 'error';
							});
						}else
						{
							$('#ok_feedback_'+methodName[0]).removeClass('import').addClass('conf');
							$('#ok_feedback_'+methodName[0]+'>img:first').attr('src', shopImporter.srcConf);
						}
						shopImporter.idMethod ++;
						shopImporter.limit = 0;
						shopImporter.getDatas(conf[shopImporter.idMethod]);
					}
					else
					{
						if (shopImporter.idMethod < shopImporter.nbrMethod)
						{
							shopImporter.limit += parseInt(shopImporter.nbr_import);
							shopImporter.getDatas(conf[shopImporter.idMethod]);
						}
						else
							shopImporter.displayEnd(true);
					}	
				}
	       },
	       error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
	       		$('#technical_error_feedback').fadeIn('slow');
	       		$('#checkAndSaveConfig').fadeIn('slow');
	       }
	   });
	   }
	},
	
	truncatTable : function (className)
	{
		if (!$('#truncat_feedback').length)
		{
			$('#steps').html($('#steps').html()+'<div id="truncat_feedback" style="display:none;" class="conf"><img src="'+this.srcConf+'">'+truncateTable+'</div>');
			$('#truncat_feedback').css('display', '');
		}
		
		$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&token='+this.token+'&truncatTable&className='+className+this.specificOptions ,
	       success: function(jsonData)
	       {		      
		       	var jsonError;
		       	if (jsonData.hasError)
	    		{
	    			jsonError = '';
	    			for (i=0;i<jsonData.error.length;i++)
							jsonError = jsonError+'<li>Table : '+jsonData.error[i]+'</li>';
					$('#truncat_feedback').removeClass('import').addClass('error');
					$('#truncat_feedback >img:first').attr('src', shopImporter.srcError);
					if ($('#display_error_truncat_feedback_link').length == 0)
					{
						$('#truncat_feedback').html($('#truncat_feedback').html()+'<span id="display_error_truncat" style="float:right;"><a id="display_error_truncat_link" class="display_error_link" rel="truncat" href="#" onclick="enableShowErrorsTruncate(); return false;">'+showErrors+'</a></span><div style="display:none;" id="feedback_truncat_errors_list"><ul>'+jsonError+'</ul></div>');
					}
					else
					{
						var nbrErrors = $('#nbr_errors_'+methodName[0]).html();
						var newNbrError = parseInt(jsonData.error.length) + parseInt(nbrErrors);
						$('#nbr_errors_'+methodName[0]).html(newNbrError);
						$('#feedback_'+methodName[0]+'_errors_list > ul').html($('#feedback_'+methodName[0]+'_errors_list > ul').html() + jsonError);
					}
	    		}
	    	},
	    	error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">TECHNICAL ERROR<br><br>Details: '+XMLHttpRequest.responseText+'</div>');
	       		$('#technical_error_feedback').fadeIn('slow');
	       		$('#checkAndSaveConfig').fadeIn('slow');
	       }
	    	
		});
	},
	
	displayEnd : function (finish)
	{	
		
		if ((this.hasErrors != 0 || ($('.display_error_link').length == 0 && this.hasErrors == 0)) || (this.hasErrors == 1))
		{
			if (this.save)
			{
				$('#steps').html($('#steps').html()+'<div id=\'ok_feedback_end\' style=\'display:none;\' class=\'conf\'><img src=\''+shopImporter.srcConf+'\'>'+importFinish+'</div>');
				$('#ok_feedback_end').fadeIn('slow');
			}
			else
			{
				$('#steps').html($('#steps').html()+'<input style="display:none" type="button" name="submitImport" id="submitImport" class="button" value="'+runImport+'">');
				$('#submitImport').fadeIn('slow', function() {
					$(this).unbind('click').click(function() {
					$.scrollTo($("#steps"), 300 , {
						onAfter:function(){
							$('#steps').html('');
							shopImporter.save = 1;
							if(type_connector == 'ws')
								shopImporter.checkAndSaveConfigWS(shopImporter.save);
							else if (type_connector == 'db')
							shopImporter.checkAndSaveConfig(shopImporter.save);
							}
						});
					});
				});
			}
		}
		else
		{
			$('#steps').html($('#steps').html()+'<div id="technical_error_feedback" style="display:none;" class="error"><img src="'+shopImporter.srcError+'">'+importHasErrors+'</div>');
			$('#technical_error_feedback').fadeIn('slow');
		}
		$('#checkAndSaveConfig').fadeIn('slow');
	},	

};

function enableShowErrors(methodName)
{
	$(document).find('#feedback_'+methodName+'_errors_list').slideToggle();
	return false;
}

function enableShowErrorsTruncate()
{
	$(document).find('#feedback_truncat_errors_list').slideToggle();
	return false;
}

function displaySpecificOptions(moduleName, server, user, password, database, prefix, token)
{
	$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "html",
	       data: 'ajax=true&token='+token+'&displaySpecificOptions&moduleName='+moduleName+'&server='+ server+'&user='+user+'&password='+password+'&database='+database+'&prefix='+prefix ,
	       success: function(htmlData)
	       {
	       		if (htmlData != 'not_exist')
	       		{
	       			$('#specificOptionsContent').html(htmlData);
	       			$('#specificOptions').show();
	       			$('#importOptions').show();
	       		}
	       },
	       error: function(XMLHttpRequest, textStatus, errorThrown)
		   {
		   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
		   }
	   });
}
function initConnexion (moduleName, url, loginws, apikey, token)
{
	$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=true&token='+token+'&connexionWs&moduleName='+moduleName+'&url='+ url+'&loginws='+loginws+'&apikey='+apikey ,
	       success: function(jsonData)
	       {	
	    	   var jsonError = '';
	    	   $('#connectionInformation').removeAttr('style');
	    	   	if (jsonData.hasError)
	    		{
	    	   		$('#connectionInformation').attr('style','width: 400px;background-color: #FAE2E3;border: 1px solid #EC9B9B');
	    			for (i=0;i<jsonData.error.length;i++)
							jsonError = jsonError+'<li>'+jsonData.error[i]+'</li>';
	    			$('#connectionInformation').slideDown('slow');
	    			$('#connectionInformation').html('<ul>'+jsonError+'</ul>');
	     			$('#connectionInformation').show();
				}else
				{
					
					$('#connectionInformation').attr('style','width: 400px;background-color: #DFFAD3;border: 1px solid #72CB67');
					$('#connectionInformation').slideDown('slow');
	    			$('#connectionInformation').html('<ul>Connection successful</ul>');
	     			$('#connectionInformation').show();
					$('#importOptions').show();
					displaySpecificOptionsWsdl(moduleName, token);
				}
	       },
	       error: function(XMLHttpRequest, textStatus, errorThrown)
		   {
		   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
		   }
	   });
}

function displaySpecificOptionsWsdl(moduleName,token)
{

	$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : 'html',
	       data: 'ajax=true&token='+token+'&displaySpecificOptionsWsdl&moduleName='+moduleName ,
	       success: function(htmlData)
	       {
	    	   	$('#specificOptionsContent').html(htmlData);
	    	   	$('#specificOptions').hide();
				$('#importOptions').show();
	       },
	       error: function(XMLHttpRequest, textStatus, errorThrown)
		   {
		   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
		   }
	   });
}
function validateSpecificOptions(moduleName, specificOptions)
{
	$.ajax({
	       type: 'GET',
	       url: '../modules/shopimporter/ajax.php',
	       async: false,
	       cache: false,
	       dataType : 'json',
	       data: 'ajax=true&token='+token+'&validateSpecificOptions&moduleName='+moduleName+specificOptions ,
	       success: function(jsonData)
	       {
	       		var jsonError = '';
	       		if (jsonData.hasError)
	    		{
	    			for (i=0;i<jsonData.error.length;i++)
							jsonError = jsonError+'<li>'+jsonData.error[i]+'</li>';
	    			$('#specificOptionsErrors').html('<ul>'+jsonError+'</ul>');
	    			$('#specificOptionsErrors').fadeIn('slow');
				}
	       },
	       error: function(XMLHttpRequest, textStatus, errorThrown)
		   {
		   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
		   }
	   });
	   
	    if ($('#specificOptionsErrors').html().length != 0)
	 		return false;
		else
	 		return true;
}

//init configuration connector (database or webservice)
function initConfigConnector()
{
	if($('#choose_module_name').attr('value'))
	{
		$('#db_config').hide();
		$('#importOptions').hide();
		$('#steps').html('');

		if ($('#import_module_name').attr('value') != 0)
		{
			$('#displayOptions').show();
			$.ajax({
			       type: 'GET',
			       url: '../modules/shopimporter/ajax.php',
			       async: false,
			       cache: false,
			       dataType : 'html',

			       data: 'ajax=true&token='+globalAjaxShopImporterToken+'&displayConfigConnector&moduleName='+$('#import_module_name').val() ,
			       success: function(html)
			       {
						$('#config_connector').html(html);
						$('#config_connector').show();	
						$('#db_config').slideDown('slow');
						$('#displayOptions').show();
						$('#checkAndSaveConfig').show();
			       },
			       error: function(XMLHttpRequest, textStatus, errorThrown)
				   {
				   		alert('TECHNICAL ERROR\nDetails:\nError thrown: ' + XMLHttpRequest + '\n' + 'Text status: ' + textStatus);
				   }
			   });
		}
		else
		{
			$('#db_config').slideUp('slow');
			$('#checkAndSaveConfig').show();
			
		}		
	}
}

$(document).ready(function(){
	$('#displayOptions').hide();
	$('#db_input input').each(function () {
		$(this).keyup(function () {
			$('#steps').fadeOut(200, function () {
				$(this).html('');
				$('#steps').fadeIn();
			});
			$('#importOptions').fadeOut('slow');
			$('#displayOptions').show();
			$('#checkAndSaveConfig').show();
		})
	});
	
	$('input[name=hasErrors]:radio').change(function () {
		if ($(this).val() == 1)
			$('#warnSkip').fadeIn('slow');
		else
			$('#warnSkip').fadeOut('slow');
	});
	
	$('#choose_module_name').unbind('click').click(function(){
		initConfigConnector();
	});
	
	$('#displayOptions').unbind('click').click(function(){
			$('#displayOptions').show();
		if(type_connector == 'ws')
		{
			if($('#loginws').val() == '' || $('#apikey').val() == '' || $('#url').val() == '')
			{
				$('#connectionInformation').slideDown('slow');
    			$('#connectionInformation').html('Url wsdl, User name,API key are required fields');
     			$('#connectionInformation').show();
				return false;
			}else{
				$('#connectionInformation').hide();
		}
			token = globalAjaxShopImporterToken;
			initConnexion($('#import_module_name').val(), $('#url').val(), $('#loginws').val(), $('#apikey').val(), token);

		return false;
		}
		else if (type_connector == 'db')
		{
			moduleName = $('#import_module_name').val();
			server = $('#server').val();
			user = $('#user').val();
			password = $('#password').val();
			database = $('#database').val();
			prefix = $('#prefix').val();
			token = globalAjaxShopImporterToken;
		displaySpecificOptions(moduleName, server, user, password, database, prefix, token);
		}
		$(this).fadeOut('slow');
		$('#importOptions').slideDown('slow');
		
		return false;
	});	
	
	$('#checkAndSaveConfig').unbind('click').click(function(){
		$('#steps, #specificOptionsErrors').html('');
		$('#specificOptionsErrors').hide();
		shopImporter.specificOptions = '';
		$('#specificOptionsContent :input').each(function (){
			shopImporter.specificOptions = shopImporter.specificOptions+'&'+$(this).attr('name')+'='+$(this).attr('value');
		});
		shopImporter.imagesOptions = '';
		$('.importImages:input:checked').each(function (){
			shopImporter.imagesOptions = shopImporter.imagesOptions+'&'+$(this).attr('name');
		});
		moduleName = $('#import_module_name').val();
		if (validateSpecificOptions(moduleName, shopImporter.specificOptions) == true)
		{
			if(type_connector == 'ws')
			{
				$.scrollTo($('#steps'), 300 , {
				onAfter:function(){
								shopImporter.specificOptions = '';
								$('#specificOptionsContent :input').each(function (){
									shopImporter.specificOptions = shopImporter.specificOptions+'&'+$(this).attr('name')+'='+$(this).attr('value');
								});
									shopImporter.idMethod = 0;
									shopImporter.limit = 0;
									shopImporter.nbr_import = parseInt($('#nbr_import').val());
									shopImporter.save = 0;

									shopImporter.moduleName = $('#import_module_name').val();
										shopImporter.url = $('#url').val();
										shopImporter.loginws = $('#loginws').val();
										shopImporter.apikey = $('#apikey').val();

										shopImporter.token = globalAjaxShopImporterToken;
										shopImporter.hasErrors = $('input[name=hasErrors]:radio:checked').val();
										
										shopImporter.checkAndSaveConfigWS(shopImporter.save);
										shopImporter.checkAndSaveConfigWSDL();
									return false;
					}
				});
			}else if (type_connector == 'db')
			{
				$.scrollTo($('#steps'), 300 , {
					onAfter:function(){
									shopImporter.specificOptions = '';
									$('#specificOptionsContent :input').each(function (){
										shopImporter.specificOptions = shopImporter.specificOptions+'&'+$(this).attr('name')+'='+$(this).attr('value');
									});
										shopImporter.idMethod = 0;
										shopImporter.limit = 0;
										shopImporter.nbr_import = parseInt($('#nbr_import').val());
										shopImporter.save = 0;
										shopImporter.moduleName = $('#import_module_name').val();
									shopImporter.server = $('#server').val();
									shopImporter.user = $('#user').val();
									shopImporter.password = $('#password').val();
									shopImporter.database = $('#database').val();
									shopImporter.prefix = $('#prefix').val();
									shopImporter.token = globalAjaxShopImporterToken;
									shopImporter.hasErrors = $('input[name=hasErrors]:radio:checked').val();
									shopImporter.checkAndSaveConfig(shopImporter.save);
								return false;
				}
			});
		}
		}
	});	
	
	$('#importOptionsYesNo :radio').change( function () {
		$('#steps').html('');
		onThing = false;
		
		$('#importOptionsYesNo :radio:checked').each( function () {
			if ($(this).attr('value') == 1)
				onThing = true;
		});
		if (onThing)
			$('#checkAndSaveConfig').fadeIn();
		else
		{
			$('#checkAndSaveConfig').fadeOut();
			$('#steps').html('<div id=\'one_thing_error_feedback\' style=\'display:none;\' class=\'error\'><img src=\''+shopImporter.srcError+'\'>'+oneThing+'</div>');
			$('#one_thing_error_feedback').fadeIn('slow');
		}			
	});
});
