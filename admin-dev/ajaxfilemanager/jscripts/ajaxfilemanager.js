/*
/*
	* author: Logan Cai
	* Email: cailongqun [at] yahoo [dot] com [dot] cn
	* Website: www.phpletter.com
	* Created At: 21/April/2007
	* Modified At: 1/June/2007
*/
// Returns true if the passed value is found in the
// array. Returns false if it is not.
Array.prototype.inArray = function (value,caseSensitive)
{
	var i;
	for (i=0; i < this.length; i++) 
	{
		// use === to check for Matches. ie., identical (===),
		if(caseSensitive){ //performs match even the string is case sensitive
		if (this[i].toLowerCase() == value.toLowerCase()) 
		{
			return true;
		}
		}else
		{
			if (this[i] == value) 
			{
				return true;
			}
		}
	}
	return false;
};
 var dcTime=250;    // doubleclick time
 var dcDelay=100;   // no clicks after doubleclick
 var dcAt=0;        // time of doubleclick
 var savEvent=null; // save Event for handling doClick().
 var savEvtTime=0;  // save time of click event.
 var savTO=null;    // handle of click setTimeOut
 var linkElem = null;
 
 
 function hadDoubleClick() 
 {
   var d = new Date();
   var now = d.getTime();
   if ((now - dcAt) < dcDelay) 
   {
     return true;
   }
   return false;
 };

 
/**
*	enable left click to preview certain files
*/
function enablePreview(elem, num)
{
		
		$(elem).each(
				 function()
				 {
					 
					 $(this).click(function ()
					{
						
						//alert('single click');
						var num = getNum(this.id);
						var path = files[num].path;
						//alert('now: ' + now + '; dcat: ' + dcAt + '; dcDelay: ' + dcDelay);
						if (hadDoubleClick())
						{
							return false;
						}else
						{
							linkElem = $('#a' + num).get(0);
						}	
						
				       d = new Date();
						savEvtTime = d.getTime();
						savTO = setTimeout(function()
						{
						if (savEvtTime - dcAt > 0) 
						{
						//check if this file is previewable
						
						
						var ext = getFileExtension(path);
						var supportedExts = supporedPreviewExts.split(",");
						var isSupportedExt = false;
						for (i in supportedExts)
						{
							var typeOf = typeof(supportedExts[i]);
							//alert(supportedExts[i]);
							if(typeOf.toLowerCase() == 'string' && supportedExts[i].toLowerCase() == ext.toLowerCase())
							{
								isSupportedExt = true;
								break;
							}
						
						}
												
						if(isSupportedExt)
						{
							switch(files[num].cssClass)
							{
								case 'fileVideo':
								case 'fileMusic':
								case 'fileFlash':
																											
									$('#playGround').html('<a id="playGround' + num + '" href="' + files[num].path + '"><div id="player">&nbsp;this is mine</div></a> ');
									
									
									$('#playGround' + num).html('');																		
									$('#playGround' + num).media({ width: 255, height: 210,  autoplay: true  });		
									//alert($('#playGround' + num).html());																	
									showThickBox($('#a' + num).get(0), appendQueryString('#TB_inline', 'height=250'  + '&width=256' + '&inlineId=winPlay&modal=true'));
						
									break;
								default:
									showThickBox(linkElem, appendQueryString(path, 'KeepThis=true&TB_iframe=true&height=' + thickbox.height + '&width=' + thickbox.width));	
									
							}
							
						}
						
						}
						
						
						return false;															
						
						}, dcTime);	
																	 
																	 return false;
																	 
																	 });
					$(this).dblclick(function()
					{
					   var d = new Date();
					   dcAt = d.getTime();
					   if (savTO != null) {
					     clearTimeout( savTO );          // Clear pending Click  
					     savTO = null;
					     
					   }
					  
					   if(typeof(selectFile) != 'undefined')
					   {
					   	
					   	 selectFile(files[num].url);
					   }else
							generateDownloadIframe(appendQueryString(getUrl('download'), 'path=' + files[num].path, ['path']));					   {
					   	
					   }
					   						
					}
					);

				 }
				 );
};
/**
* add over class to the specific table
*/
function tableRuler(element)
{
	
    var rows = $(element);
	
    $(rows).each(function(){
        $(this).mouseover(function(){
            $(this).addClass('over');
        });
        $(this).mouseout(function(){
            $(this).removeClass('over');
        });
    });
};

			





function previewMedia(rowNum)
{
	$('#preview' +rowNum).html('');
	$('#preview' +rowNum).media({ width: 255, height: 210,  autoplay: true  });
	return false;
};

function getFileExtension(filename) 
{ 
 if( filename.length == 0 ) return ""; 
 var dot = filename.lastIndexOf("."); 
 if( dot == -1 ) return ""; 
 var extension = filename.substr(dot + 1,filename.length); 
 return extension; 
}; 

function closeWindow()
{
	if(window.confirm(warningCloseWindow))
	{
		window.close();
	}
	return false;
};

/**
*	return the url with query string
*/
function getUrl(index,limitNeeded , viewNeeded, searchNeeded)
{

	var queryStr = '';
	var excluded = new Array();
	
	if(typeof(limitNeeded) == 'boolean' && limitNeeded)
	{
		var limit = document.getElementById('limit');
		var typeLimit = typeof(limit);
		
		if(typeLimit != 'undefined' && limit )
		{
			excluded[excluded.length] = 'limit';
			queryStr += (queryStr == ''?'':'&') + 'limit=' + limit.options[limit.selectedIndex].value;				
		}
		
	}
	if(typeof(viewNeeded) == 'boolean' && viewNeeded)
	{
		queryStr += (queryStr == ''?'':'&') + 'view=' +  getView();
		excluded[excluded.length] = 'view';
		
	}
	
	if(typeof(searchNeeded) == 'boolean' && searchNeeded && searchRequired)
	{
		var search_recursively = 0;
		$('input[@name=search_recursively][@checked]').each(
															function()
															{
																search_recursively = this.value;	
															}
															);		
		var searchFolder = document.getElementById('search_folder');
		queryStr += (queryStr == ''?'':'&') + 'search=1&search_name=' + $('#search_name').val() + '&search_recursively=' + search_recursively + '&search_mtime_from=' + $('#search_mtime_from').val() + '&search_mtime_to=' + $('#search_mtime_to').val() + '&search_folder=' +  searchFolder.options[searchFolder.selectedIndex].value;
		excluded[excluded.length] = 'search';
		excluded[excluded.length] = 'search_recursively';
		excluded[excluded.length] = 'search_mtime_from';
		excluded[excluded.length] = 'search_mtime_to';
		excluded[excluded.length] = 'search_folder';
		excluded[excluded.length] = 'search_name';
		excluded[excluded.length] = 'search';
		
	}
	
	

	return appendQueryString(appendQueryString(urls[index], queryString), queryStr, excluded);
};
/**
*	change view
*/
function changeView()
{

		var url = getUrl('view', true, true);
		$('#rightCol').empty();
		ajaxStart('#rightCol');		
		
		$('#rightCol').load(url, 
					{},
					function(){
							ajaxStop('#rightCol img.ajaxLoadingImg');
							urls.present = getUrl('home', true, true);
							initAfterListingLoaded();
						});
};

function goParentFolder()
{

		searchRequired = false;
		var url = appendQueryString(getUrl('view', true, true), 'path=' + parentFolder.path , ['path']);
		$('#rightCol').empty();
		ajaxStart('#rightCol');		
		
		$('#rightCol').load(url, 
					{},
					function(){
							urls.present = appendQueryString(getUrl('home', true, true), 'path=' + parentFolder.path , ['path']);
							ajaxStop('#rightCol img.ajaxLoadingImg');
							initAfterListingLoaded();
						});

};

/**
*	append Query string to the base url
* @param string baseUrl the base url
* @param string the query string
* @param array remove thost url variable from base url if any matches
*/
function appendQueryString(baseUrl, queryStr, excludedQueryStr)
{
	
	if(typeof(excludedQueryStr) == 'object' && excludedQueryStr.length)
	{
		var isMatched = false;
		var urlParts = baseUrl.split("?");
		baseUrl = urlParts[0];
		var count = 1;
		if(typeof(urlParts[1]) != 'undefined' && urlParts[1] != '')
		{//this is the query string parts
			var queryStrParts = urlParts[1].split("&");
			for(var i=0; i < queryStrParts.length; i++)
			{
				//split into query string variable name & value
				var queryStrVariables = queryStrParts[i].split('=');
				for(var j=0; j < excludedQueryStr.length; j++)
				{
					if(queryStrVariables[0] == excludedQueryStr[j])
					{
						isMatched = true;
					}
				}	
				if(!isMatched)
				{
					baseUrl += ((count==1?'?':'&') + queryStrVariables[0] + '=' + queryStrVariables[1]);
					count++;
				}
			}
		}

	}
	if(queryStr != '')
	{
		return (baseUrl.indexOf('?')> -1?baseUrl + '&' + queryStr:baseUrl + '?' + queryStr);
	}else
	{
		return baseUrl;
	}
	
	
	
	
};


/**
*	initiate when the listing page is loaded
* add main features according to the view
*/
function initAfterListingLoaded()
{

	
	parsePagination();

	parseCurrentFolder();
	var view = getView();
	
	setDocInfo('root');
	
	if(view != '')
	{
			
		switch(view)
		{

				
			case 'thumbnail':
				//enableContextMenu('dl.thumbnailListing, dl.thumbnailListing dt, dl.thumbnailListing dd, dl.thumbnailListing a');
				enableContextMenu('dl.thumbnailListing');
				for(i in files)
				{
					if(files[i].type== 'folder')
					{//this is foder item
						
						enableFolderBrowsable(i);
					}else
					{//this is file item
						
						switch(files[i].cssClass)
						{
							case 'filePicture':
								//$('#a' + i).attr('rel', 'ajaxphotos');
								//retrieveThumbnail(i);
								
								break;
							case 'fileFlash':
								break;
							case 'fileVideo':
								break;			
							case 'fileMusic':
								break;
							default:
							
								
						}
						enablePreview('#dt' + i, i);
						enablePreview('#thumbUrl' + i, i);
						enablePreview('#a' + i, i);

					}
					enableShowDocInfo( i);
					
				}
				break;
			case 'detail':
			default:
				
				enableContextMenu('#fileList tr');
				for(i in files)
				{
					if(files[i].type== 'folder')
					{//this is foder item
						enableFolderBrowsable(i);
					}else
					{//this is file item
						switch(files[i].cssClass)
						{
							case 'filePicture':
								$('#row' + i + ' td a').attr('rel', 'ajaxphotos');
								break;
							case 'fileFlash':
								break;
							case 'fileVideo':
								break;			
							case 'fileMusic':
								break;
							default:						
								
						};	
						enablePreview('#row' + i + ' td a', i);
						
					}	
					enableShowDocInfo(i);				
				}				
				break;

			
		}
	}	
	
	
};

function enableFolderBrowsable(num, debug)
{
	
	switch(getView())
	{
		case 'thumbnail':
			$('#dt'+ num + ' , #dd' + num + ' a').each(function()
																						 
				{		
/*					if(typeof(debug) != 'undefined' && debug)
					{
						alert(this.tagName  + ' ' +  files[num].path);
					}*/
					doEnableFolderBrowsable(this, num);
				}
			);
			break;
		case 'detail':
		default:
		$('#row' + num + ' td[a]').each(function()
																						 
				{		
					doEnableFolderBrowsable(this, num );
				}
			);
			
	}
	
		
		
	
};

function doEnableFolderBrowsable(elem, num)
{
									 $(elem).click(function()
									{
									 {
									 	searchRequired = false;
									 	var typeNum = typeof(num);
									 	if(typeNum.toUpperCase() == 'STRING')
									 	{
									 		var fpath = (num.indexOf(urls.view) >=0?num:files[num].path);
									 	}else
									 	{
									 		var fpath = files[num].path;
									 	}								 	
									 	
									 	
										 var url = appendQueryString(getUrl('view', true, true), 'path=' + fpath, ['path']);
										 
										 
										 $('#rightCol').empty();	
										 ajaxStart('#rightCol');
										$('#rightCol').load(url, 
													{},
													function(){
														    urls.present = appendQueryString(getUrl('home', true, true), 'path=' + fpath, ['path']);
															ajaxStop('#rightCol img.ajaxLoadingImg');
															initAfterListingLoaded();
														});																									 
									 };
									 return false;	

								}
								);									 
};

/**
* @param mixed destinationSelector where the animation image will be append to
*	@param mixed selectorOfAnimation the jquery selector of the animation 
*/
function ajaxStart(destinationSelector, id, selectorOfAnimation)
{
	if(typeof(selectorOfAnimation) == 'undefined')
	{//set defaullt animation
		selectorOfAnimation = '#ajaxLoading img';
	}
	if(typeof(id) != 'undefined')
	{
		$(selectorOfAnimation).clone().attr('id', id).appendTo(destinationSelector);

	}else
	{
		$(selectorOfAnimation).clone(true).appendTo(destinationSelector);
		
	}
	
	
};
/**
* remove the ajax animation 
*	@param mixed selectorOfAnimation the jquery selector of the animation 
*/
function ajaxStop(selectorOfAnimation)
{
	$(selectorOfAnimation).remove();
};
/**
*	change pagination limit
*/
function changePaginationLimit(elem)
{
		var url = getUrl('view', true, true, true);
		$('#rightCol').empty();
		ajaxStart('#rightCol');				
		$('#rightCol').load(url, 
					{},
					function(){
							urls.present = appendQueryString(getUrl('home', true, true), 'path=' + parentFolder.path , ['path'])
							ajaxStop('#rightCol img.ajaxLoadingImg');
							initAfterListingLoaded();
						});	
};
/**
*	get a query string variable value from an url
* @param string index
* @param string url
*/
function getUrlVarValue(url, index)
{
	
	if(url != '' && index != '')
	{
		var urlParts = url.split("?");
		baseUrl = urlParts[0];	
		var count = 1;
		if(typeof(urlParts[1]) != 'undefined' && urlParts[1] != '')
		{//this is the query string parts
			var queryStrParts = urlParts[1].split("&");
			for(var i=0; i < queryStrParts.length; i++)
			{
				//split into query string variable name & value
				var queryStrVariables = queryStrParts[i].split('=');
				if(queryStrVariables[0] == index)
				{
					return queryStrVariables[1];
				}
			}
		}		
	}
	return '';

};
/**
*	parse current folder
*/
function parseCurrentFolder()
{
	var folders = currentFolder.friendly_path.split('/');
	var str = '';
	var url = getUrl('view', true, true);

	var parentPath = '';
	for(var i = 0; i < folders.length; i++)
	{
		if(i == 0)
		{
			parentPath += paths.root;
			str += '/<a href="' + appendQueryString(url, 'path='+ parentPath, ['path']) + '"><span class="folderRoot">' + paths.root_title + '</span></a>'
			
		}else
		{
			if(folders[i] != '')
			{
				
				parentPath += folders[i] + '/';
				str += '/<a href="' + appendQueryString(url, 'path='+ parentPath , ['path']) + '"><span class="folderSub">' + folders[i] + '</span></a>';
			}
		}
	}
	$('#currentFolderPath').empty().append(str);
	$('#currentFolderPath a').each(
																 function()
																 {
																	 doEnableFolderBrowsable(this, $(this).attr('href'));
																 }
																 );
};
/**
*	enable pagination as ajax function call
*/
function parsePagination()
{
	$('p.pagination a[@id!=pagination_parent_link]').each(function ()
																		 {
																			 $(this).click(
																										 function()
																										 {
																													

																											var page =  getUrlVarValue($(this).attr('href'), 'page');
																											var url = appendQueryString(getUrl('view', true, true, searchRequired),'page=' + page, ['page']);
																											$('#rightCol').empty();
																											ajaxStart('#rightCol');
																											$('#rightCol').load(url, 
																														{},
																														function(){
																															urls.present = appendQueryString(getUrl('home', true, true, searchRequired),'page=' + page, ['page']);
																																ajaxStop('#rightCol img.ajaxLoadingImg');
																																initAfterListingLoaded();
																															});	
																											return false;
																										 }
																										 
																										 );
																		 }
																		 );
};
/**
*	get current view
*/
function getView()
{
	var view = $('input[@name=view][@checked]').get(0);
	if(typeof(view) != 'undefined')
	{
		return view.value;
	}else
	{
		return '';
	}
};

function getNum(elemId)
{
	
	if(typeof(elemId) != 'undefined' && elemId != '')
	{
		var r = elemId.match(/[\d\.]+/g);	
		if(typeof(r) != 'undefined' &&  r &&  typeof(r[0]) != 'undefined')
		{
			return r[0];
		}		
	}

	return 0;
};

function enableContextMenu(jquerySelectors)
{
	
	$(jquerySelectors).contextMenu('contextMenu', 
																 {
																 bindings:
																 {
																		'menuSelect':function(t)
																		{
																			var num = (getNum($(t).attr('id')));	
																			
																			selectFile(files[num].url);
																		},
																		'menuPlay':function(t)
																		{
																			var num = (getNum($(t).attr('id')));																			
																			$('#playGround').html('<a id="playGround' + num + '" href="' + files[num].path + '"><div id="player">&nbsp;this is mine</div></a> ');
																			
																			
																			$('#playGround' + num).html('');																		
																			$('#playGround' + num).media({ width: 255, height: 210,  autoplay: true  });		
																			//alert($('#playGround' + num).html());																	
																			showThickBox($('#a' + num).get(0), appendQueryString('#TB_inline', 'height=250'  + '&width=258' + '&inlineId=winPlay&modal=true'));

																			
																			
																		},
																		'menuPreview':function(t)
																		{
																			var num = (getNum($(t).attr('id')));
																			$('#a' + num).click();					
																		},
																		'menuDownload':function(t)
																		{
																			var num = (getNum($(t).attr('id')));		
																			generateDownloadIframe(appendQueryString(getUrl('download', false, false), 'path=' + files[num].path, ['path']));
																		},
																		'menuRename':function(t)
																		{
																			var num = (getNum($(t).attr('id')));

																			showThickBox($('#a' + num).get(0), appendQueryString('#TB_inline', 'height=100' + '&width=350' + '&inlineId=winRename&modal=true'));
																			
																			$('div#TB_window #renameName').val(files[num].name);
																			$('div#TB_window #original_path').val(files[num].path);
																			$('div#TB_window #renameNum').val(num);																			
																		},
																		'menuEdit':function(t)
																		{
																			var num = (getNum($(t).attr('id')));
																			var url = '';
																			switch(files[num].cssClass)
																			{
																				case 'filePicture':
																					url = getUrl('image_editor');
																					break;
																				default:
																					url = getUrl('text_editor');
																					
																			}
																				 var param = "status=yes,menubar=no,resizable=yes,scrollbars=yes,location=no,toolbar=no";
																				 param += ",height=" + screen.height + ",width=" + screen.width;
																				if(typeof(window.screenX) != 'undefined')
																				{
																					param += ",screenX = 0,screenY=0";
																				}else if(typeof(window.screenTop) != 'undefined' )
																				{
																					param += ",left = 0,top=0" ;
																				}		 
																				var newWindow = window.open(url + ((url.lastIndexOf("?") > - 1)?"&":"?") + "path="  + files[num].path,'', param);
																				newWindow.focus( );																						
	
																				
																		},

																		'menuCut':function(t)
																		{
																			
																		},
																		'menuCopy':function(t)
																		{
																			
																		},
																		'menuPaste':function(t)
																		{
																			
																		},
																		'menuDelete':function(t)
																		{
																			var num = (getNum($(t).attr('id')));
																			if(window.confirm(warningDelete))
																			{
																				$.getJSON(appendQueryString(getUrl('delete', false,false), 'delete=' + files[num].path, ['delete']), 
																				function(data)
																				{
																					if(typeof(data.error) == 'undefined')
																					{
																						alert('Unexpected Error.');
																					}
																					else if(data.error != '')
																					{
																						alert(data.error);
																					}else
																					{//remove deleted files
																						switch(getView())
																						{
																							case 'thumbnail':																													$('#dl' + num ).remove();
																								break;
																							case 'detail':
																							default:
																								$('#row' + num).remove();
																								
																						}
																						files[num] = null;
																					}
																				}
																				);
																				
																							 				
																			}																			
																		}
																 },
																 	onContextMenu:function(events)
																	{
																	
																		return true;
																	},
																	onShowMenu:function(events, menu)
																	{
																		
																		switch(getView())
																		{
																			case 'thumbnail':
																				var num = getNum(events.target.id);
																		
																				break;
																			case 'detail':
																			default:
																				switch(events.target.tagName.toLowerCase())
																				{
																					case 'span':
																						
																						if($(events.target).parent().get(0).tagName.toLowerCase()  == 'a')
																						{
																							
																							var num = getNum($(events.target).parent().parent().parent().attr('id'));			
																						}else
																						{
																							var num = getNum($(events.target).parent().parent().parent().parent().attr('id'));			
																						}
																						
																						
																					
																						break;
																					case 'td':
																					var num = getNum($(events.target).parent().attr('id'));																				
																						break;
																					case 'a':
																				    case 'input':
																				      var num = getNum($(events.target).parent().parent().attr('id'));			
																				      break;
																				}
																		}
																		
																		var menusToRemove = new Array;
																		if(typeof(selectFile) == 'undefined')
																		{
																			menusToRemove[menusToRemove.length] = '#menuSelect';
																		}
																		menusToRemove[menusToRemove.length] = '#menuCut';
																		menusToRemove[menusToRemove.length] = '#menuCopy';
																		menusToRemove[menusToRemove.length] = '#menuPaste';
																		switch(files[num].type)
																		{
																			case 'folder':
																				if(numFiles < 1)
																				{
																					menusToRemove[menusToRemove.length] = '#menuPaste';
																				}
																				menusToRemove[menusToRemove.length] = '#menuPreview';
																				menusToRemove[menusToRemove.length] = '#menuDownload';
																				menusToRemove[menusToRemove.length] = '#menuEdit';		
																				menusToRemove[menusToRemove.length] = '#menuPlay';
																				menusToRemove[menusToRemove.length] = '#menuDownload';
																				
																				break;
																			default:
																			var isSupportedExt = false;
																			if(permits.edit)
																			{
																			var ext = getFileExtension(files[num].path);
																			var supportedExts = supporedPreviewExts.split(",");
																			
																			for(var i = 0; i < supportedExts.length; i++)
																			{
																			if(typeof(supportedExts[i]) != 'undefined' && typeof(supportedExts[i]).toLowerCase() == 'string' && supportedExts[i].toLowerCase() == ext.toLowerCase())
																			{
																			isSupportedExt = true;
																			break;
																			}
																			}
																				
																			}
																			if(!isSupportedExt || permits.view_only)
																			{
																				menusToRemove[menusToRemove.length] = '#menuEdit';
																			}
	
																																					
																			switch(files[num].cssClass)
																			{
																				case 'filePicture':
																					menusToRemove[menusToRemove.length] = '#menuPlay';
																					break;
																				case 'fileCode':
																					menusToRemove[menusToRemove.length] = '#menuPlay';
																					break;
																				case 'fileVideo':
																				case 'fileFlash':
																				case 'fileMusic':
																				
																					menusToRemove[menusToRemove.length] = '#menuPreview';																					menusToRemove[menusToRemove.length] = '#menuEdit';
																					break;
																				default:
																					menusToRemove[menusToRemove.length] = '#menuPreview';
																					menusToRemove[menusToRemove.length] = '#menuPlay';
																					
																					
																					
																			}
																			menusToRemove[menusToRemove.length] = '#menuPaste';
																		}
																		if(!permits.edit|| permits.view_only)
																		{
																			menusToRemove[menusToRemove.length] = '#menuEdit';
																		}																		
																		if(!permits.del || permits.view_only)
																		{
																			menusToRemove[menusToRemove.length] = '#menuDelete';
																		}  
																		if(!permits.cut || permits.view_only)
																		{
																			menusToRemove[menusToRemove.length] = '#menuCut';
																		} 
																		if(!permits.copy || permits.view_only)
																		{
																			menusToRemove[menusToRemove.length] = '#menuCopy';
																		} 
																		if((!permits.cut  && !permits.copy) || permits.view_only)
																		{
																			menusToRemove[menusToRemove.length] = '#menuPaste';
																		} 
																		if(!permits.rename || permits.view_only)
																		{
																			menusToRemove[menusToRemove.length] = '#menuRename';
																		} 
																																																																						
																		//alert(menusToRemove.join(','));
																		var txt = '';
																		for(var t in menu)
																		{
																			//txt += t + ': ' + menu[t] + '\n';
																		}
																		$(menu).children().children().children().each(
																			function()
																			{
																				if(menusToRemove.inArray('#' + this.id))
																				{
																					$(this).parent().remove();
																				}
																				//alert(this.id);
															
																				
																			}
																		)
																		//alert(menusToRemove.join(','));
																		//$(menusToRemove.join(','), $(menu).children().children().children()).remove();								
																		
																
																		return menu;
																	}
																 }
																 );	
};


var fileUploadElemIds = new Array(); //keep track of the file element ids
/**
*	add more file type of input file for multiple uploads
*/
function addMoreFile()
{
	
	var newFileUpload = $($('div#TB_window #fileUploadBody  tr').get(0)).clone();
	
	do
	{
		var elementId = 'upload' + generateUniqueId(10);
	}while(fileUploadElemIds.inArray(elementId));
	
	fileUploadElemIds[fileUploadElemIds.length] = elementId;	

	$(newFileUpload).appendTo('div#TB_window #fileUploadBody');
	$('input[@type=file]', newFileUpload).attr('id', elementId);
	$('span.uploadProcessing', newFileUpload).attr('id', 'ajax' + elementId);
	$('input[@type=button]', newFileUpload).click(
		function()
		{
			uploadFile(elementId);
		}
	);
	$('a', newFileUpload).show().click(
		function()
		{
			cancelFileUpload(elementId);
		}
	);


	$(newFileUpload).show();
	
	return false;
};
/**
*	cancel uploading file
*   remove hidden upload frame
*   remove hidden upload form
*/
function cancelFileUpload(elementId)
{
	$('div#TB_window #' + elementId).parent().parent().remove();

	//ensure there is at least one visible upload element
	while($('div#TB_window #fileUploadBody tr').length < 2)
	{
		addMoreFile();
	}
	return false;	
};
/**
*	upload file
*/
function uploadFile(elementId)
{

		var ext = getFileExtension($('#' + elementId).val());
		if(ext == '')
		{
			alert(noFileSelected );
			return false;
		}
		var supportedExts = supportedUploadExts.split(",");
		var isSupportedExt = false;
		
		for (i in supportedExts)
		{
			//alert(typeof(supportedExts[i]));
			if(typeof(supportedExts[i]) == 'string')
			{
				isSupportedExt = true;
				break;				
			}
		}	
		
		if(!isSupportedExt)
		{
			alert(msgInvalidExt);
			return false;
		}
	
		$('#ajax' + elementId).hide();
		$('#ajax' + elementId).show();
		$.ajaxFileUpload
		(
			{
				url:appendQueryString(getUrl('upload', false, false), 'folder=' + currentFolder.path, ['folder']),
				secureuri:false,
				fileElementId:elementId,
				dataType: 'json',
				success: function (data, status)
				{
					
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
							$('#ajax' + elementId).hide();
						}else
						{
							//remove the file type of input
							cancelFileUpload(elementId);
							numRows++;
							files[numRows] = {};

							for(var i in data)
							{
								if(i != 'error')
								{
									files[numRows][i] =  data[i];
								}
							}
							addDocumentHtml(numRows);
						}
					}
					
				},
				error: function (data, status, e)
				{
					$('#ajax' + elementId).hide();
					alert(e);
				}
			}
		)	
	
	return false;
};
/**
*	 generate unique id
*/
function generateUniqueId(leng)
{
   var idLength = leng || 32;
   var chars = "0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";
   var id = '';
   for(var i = 0; i <= idLength; i++)
   {
      id += chars.substr( Math.floor(Math.random() * 62), 1 );
   }
   
   return (id );   
   
};

/**
*	generate a hidden iframe and force to download the specified file 
*/
function generateDownloadIframe(url)
{
				var frameId = 'ajaxDownloadIframe';		
				$('#' + frameId).remove();
				if(window.ActiveXObject) {
						var io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
						
						
				}
				else {
						var io = document.createElement('iframe');
						io.id = frameId;
						io.name = frameId;
				}
				io.style.position = 'absolute';
				io.style.top = '-1000px';
				io.style.left = '-1000px';
				io.src = url; 
				document.body.appendChild(io);		
};


/**
*	show the url content in thickbox
*/
function showThickBox(linkElem, url)
{
	$(linkElem).attr('href', url);
	var t = linkElem.title || linkElem.name || null;
	var a = linkElem.href || linkElem.alt;
	var g = linkElem.rel || false;
	tb_show(t,a,g);
	linkElem.blur();	
	return false;
};
/**
*	bring up a file upload window
*/
function uploadFileWin(linkElem)
{
	showThickBox(linkElem, appendQueryString('#TB_inline', 'height=200' + '&width=500' + '&inlineId=winUpload&modal=true'));
	while($('div#TB_window #fileUploadBody tr').length < 2)
	{
		addMoreFile();
	}

};
/**
*	bring up a new folder window
*/
function newFolderWin(linkElem)
{
	showThickBox(linkElem, appendQueryString('#TB_inline', 'height=100'  + '&width=250' + '&inlineId=winNewFolder&modal=true'));
	return false;
};
/**
*	ajax call to create a folder
*/
function doCreateFolder()
{
	$('div#TB_window  #currentNewfolderPath').val(currentFolder.path);
	var pattern=/^[A-Za-z0-9_ \-]+$/i;
	
	var folder = $('div#TB_window #new_folder');
	//alert($('#new_folder').val());
	if(!pattern.test($(folder).val()))
	{
		
		
		alert(msgInvalidFolderName);	
	}else
	{	
			var options = 
			{ 
				dataType: 'json',
				url:getUrl('create_folder'),
				error: function (data, status, e) 
				{

					alert(e);
				},				
				success:   function(data) 
				{ 
					//remove those selected items
					if(data.error != '')
					{
						alert(data.error);
					}else
					{
						
							numRows++;
							files[numRows] = {};
							for(var i in data)
							{
								if(i != 'error')
								{
									files[numRows][i] =  data[i];
								}
							}
						addDocumentHtml(numRows);

						tb_remove();
						
																

					}
										
					


				} 
			}; 
			$('div#TB_window  #formNewFolder').ajaxSubmit(options); 	
						 				
				
	}
	return false;	
	
};
/**
* selecte documents and fire an ajax call to delete them
*/
function deleteDocuments(msgNoDocSelected, msgUnableToDelete, msgWarning, elements)
{
	if(!window.confirm(warningDel))
	{
		return false;
	}
	switch(getView())
	{
		case 'thumbnail':
			var selectedDoc = $('#rightCol dl.thumbnailListing input[@type=checkbox][@checked]');
			break;
		case 'detail':
			
		default:
			var selectedDoc = $('#fileList input[@type=checkbox][@checked]');
	}
	
	var hiddenSelectedDoc = document.getElementById('selectedDoc');
	var selectedOptions;
	var isSelected = false;
	
	//remove all options
	$(hiddenSelectedDoc).removeOption(/./);
	$(selectedDoc).each(function(i){
										
										$(hiddenSelectedDoc).addOption($(this).val(), getNum($(this).attr('id')), true);
										isSelected = true;
										 });		
	if(!isSelected)
	{
		alert(msgNoDocSelected);
	}
	else
	{//remove them via ajax call
			var options = 
			{ 
				dataType: 'json',
				url:getUrl('delete'),
				error: function (data, status, e) 
				{

					alert(e);
				},				
				success:   function(data) 
				{ 
					if(typeof(data.error) == 'undefined')
					{
						alert('Unexpected error.');
					}else if(data.error != '')
					{
						alert(data.error);
					}else
					{
						
						//remove all files
						for(var i =0; i < hiddenSelectedDoc.options.length; i++)
						{
							switch(getView())
							{
								case 'thumbnail':
									$('#dl' + hiddenSelectedDoc.options[i].text).remove();
									break;
								case 'detail':
								default:
									$('#row' + hiddenSelectedDoc.options[i].text).remove();
							}
						}											

					}
				} 
			}; 
			$('#formAction').ajaxSubmit(options); 			

			
		
	}
	
	return false;	
};
/**
*	renmae the specific file/folder
*/
function doRename()
{
	
	var num = $('div#TB_window #renameNum').val();
	if(files[num].fileType == 'folder')
	{
		var pattern=/^[A-Za-z0-9_ \-]+$/i;
	}else
	{
		var pattern=/^[A-Za-z0-9_ \-\.]+$/i;
	}	
	
	if(!pattern.test($('div#TB_window  #renameName').val()))
	{
		if(files[num].fileType == 'folder')
		{
			alert(msgInvalidFolderName);
		}else
		{
			alert(msgInvalidFileName);
		}	
			
	}else
	{	
			var options = 
			{ 
				dataType: 'json',
				url:getUrl('rename'),
				error: function (data, status, e) 
				{

					alert(e);
				},				
				success:   function(data) 
				{ 
					//remove those selected items
					if(data.error != '')
					{
						alert(data.error);
					}else
					{
						
						var info = '';
						for(var i in data)
						{
							if(i != 'error')
							{
								files[num][i] = data[i];
							}
							
						}						
						switch(getView())
						{
							case 'thumbnail':
							 	$('#thumbUrl' + num).attr('href', files[num].path);
							 	$('#thumbImg' + num).attr('src', appendQueryString(getUrl('thumbnail'), 'path=' + files[num].path, ['path']));
							 	$('#cb' + num).val(files[num].path);
							 	$('#a' + num).attr('href', files[num].path).text(files[num].name);
								break;
	
							case 'detail':

							default:
								$('#check' + num).val(files[num].path);								
								$('#a' + num).attr('href', files[num].path);							
								$('#tdnd' + num).text(files[num].name);
								$('#tdth' + num).text(files[num].name);								
						}
						
						tb_remove();											

					}
				} 
			}; 
			$('div#TB_window #formRename').ajaxSubmit(options); 	
						 				
				
	}	
};
/**
* reload the whole window 
*/
function windowRefresh()
{
	document.location.href = urls.present;
	//document.location.reload();
};
/**
*	show the system information
*/
function infoWin(linkElem)
{
	
	showThickBox(linkElem, appendQueryString('#TB_inline', 'height=180' + '&width=500'+ '&inlineId=winInfo&modal=true'));

};
/**
*check all checkboxs and uncheck all checkbox
*/
function checkAll(checkbox)
{

	if($(checkbox).attr('class') == "check_all")
	{
		$('#tickAll, #actionSelectAll').attr('class', 'uncheck_all');
		$('#tickAll, #actionSelectAll').attr('title', unselectAllText);
		$('#actionSelectAll span').html(unselectAllText);
		switch(getView())
		{
			case 'thumbnail':
				$('#rightCol dl.thumbnailListing input[@type=checkbox]').each(function(i){
														   			$(this).attr("checked", 'checked');												 
																	 })	;
				break;
			case 'detail':
			default:
$("#fileList tr[@id^=row] input[@type=checkbox]").each(function(i){
														   			$(this).attr("checked", 'checked');												 
																	 })	;			
		}
				

	}else
	{
		$('#tickAll, #actionSelectAll').attr('class', 'check_all');
		$('#tickAll, #actionSelectAll').attr('title', selectAllText);		
		$('#actionSelectAll span').html( selectAllText);	
		switch(getView())
		{
			case 'thumbnail':
				$('#rightCol dl.thumbnailListing input[@type=checkbox]').each(function(i){
														   			$(this).removeAttr("checked");											 
																	 })	;
				break;
			case 'detail':
			default:
$("#fileList tr[@id^=row] input[@type=checkbox]").each(function(i){
														   			$(this).removeAttr("checked");											 
																	 })	;			
		}		
	}

	return false;
		
};		

function cutDocuments(msgNoDocSelected)
{
	repositionDocuments(msgNoDocSelected, getUrl('cut'), 'cut');
	return false;
};

function copyDocuments(msgNoDocSelected)
{
	repositionDocuments(msgNoDocSelected, getUrl('copy'), 'copy');
	return false;
};

/**
* selecte documents and fire an ajax call to delete them
*/
function repositionDocuments(msgNoDocSelected, formActionUrl, actionVal)
{
	switch(getView())
	{
		case 'thumbnail':
			var selectedDoc = $('#rightCol dl.thumbnailListing input[@type=checkbox][@checked]');
			break;
		case 'detail':
			
		default:
			var selectedDoc = $('#fileList input[@type=checkbox][@checked]');
	}
	
	var hiddenSelectedDoc = document.getElementById('selectedDoc');
	var selectedOptions;
	var isSelected = false;
	
	//remove all options
	$(hiddenSelectedDoc).removeOption(/./);
	$(selectedDoc).each(function(i){
										
										$(hiddenSelectedDoc).addOption($(this).val(), getNum($(this).attr('id')), true);
										isSelected = true;
										 });		
	if(!isSelected)
	{
		alert(msgNoDocSelected);
	}
	else
	{
		
			var formAction =  document.formAction;
			var actionElem = $('#action_value');
			formAction.action = formActionUrl;

			$('#currentFolderPathVal').val(currentFolder.path);
			$(actionElem).val(actionVal);
			var options = 
			{ 
				dataType: 'json',
				error: function (data, status, e) 
				{
					alert(e);
				},				
				success:   function(data) 
				{ 
										if(typeof(data.error) == 'undefined')
										{
											alert('Unexpected Error');
										}
										else if(data.error != '')
										{
											alert(data.error);
										}else
										{			
											//set change flags
											numFiles = parseInt(data.num);
											var flag = (actionVal == 'copy'?'copyFlag':'cutFlag');		
											action = actionVal;
											//clear all flag
											for(var i = 1; i < numRows; i++)
											{
												$('#flag' + i).attr('class', 'noFlag');
											}											
											for(var i =0; i < hiddenSelectedDoc.options.length; i++)
											{
												$('#flag' + hiddenSelectedDoc.options[i].text).attr('class', flag);
											}

										}
										
					} 
			}; 
			$(formAction).ajaxSubmit(options); 	
						 				

	}
	
	return false;	
};

function pasteDocuments(msgNoDocSelected)
{
	if(numFiles)
	{

		var warningMsg = (action == 'copy'?warningCopyPaste:warningCutPaste);
		if(window.confirm(warningMsg))
		{
			$.getJSON(appendQueryString(getUrl('paste'), 'current_folder_path='+ currentFolder.path, ['current_folder_path']), 
				function(json)
				{
					if(typeof(json.error) == 'undefined')
					{
						alert('Unexpected Error.');
					}
					{
						
						if(json.error != '')
						{
							alert(json.error);
						}
						
						
						for(var j in json.files)
						{
							numRows++;
							files[numRows] = {};
							for(var i in json.files[j])
							{
								files[numRows][i] = json.files[j][i];
							}
							addDocumentHtml(numRows);						
							
						}
						numFiles = parseInt(json.unmoved_files);
						
						
						
						
					}
				}
			);
		
		}
	}else
	{
		alert(msgNoDocSelected);
	}
	return false;
	
};
/**
*	add document item html to the file listing body
*/
function addDocumentHtml(num)
{
		var strDisabled = "";
		if(!files[num].is_writable)
		{
			strDisabled = "disabled";
		}	
		switch(getView())
		{
			
			case 'thumbnail':
				$(
				'<dl class="thumbnailListing" id="dl' + num + '" ><dt id="dt' + num + '" class="' + files[num].cssClass + '"></dt><dd id="dd' + num + '" class="thumbnailListing_info"><span id="flag' + num + '" class="' + files[num].flag + '">&nbsp;</span><input id="cb' + num + '" type="checkbox"  class="radio" ' + strDisabled +' name="check[]" class="input" value="' + files[num].path + '" /><a href="' + files[num].path + '" title="' + files[num].name + '" id="a' + num + '">' + (typeof(files[num].short_name) != 'undefined'?files[num].short_name:files[num].name) + '</a></dd></dl>').appendTo('#content');
			

				if(files[num].type== 'folder')
				{//this is foder item
					
					enableFolderBrowsable(num);
				}else
				{//this is file item
					
					switch(files[num].cssClass)
					{
						case 'filePicture':
							$('<a id="thumbUrl' + num + '" rel="thumbPhotos" href="' + files[num].path + '"><img src="' + appendQueryString(getUrl('thumbnail', false, false), 'path=' + files[num].path, ['path']) + '" id="thumbImg' +  num + '"></a>').appendTo('#dt' + num);
							break;
						case 'fileFlash':
							break;
						case 'fileVideo':
							break;			
						case 'fileMusic':
							break;
						default:
						
							
					}
					enablePreview('#dl' + num + ' a', [num]);					
				
				}	
				enableContextMenu('#dl' + num);	
				enableShowDocInfo( num);									
				break;
			case 'detail':
			default:
				var cssRow = (num % 2?"even":"odd");
				$('<tr class="' + cssRow + '" id="row' + num + '"><td id="tdz' + num +'" align="center"><span id="flag' + num +'" class="' + files[num].flag +'">&nbsp;</span><input type="checkbox" class="radio" name="check[]" id="cb' + num +'" value="' + files[num].path +'" ' + strDisabled + ' /></td><td align="center" class="fileColumns"   id="tdst1">&nbsp;<a id="a' + num +'" href="' + files[num].path +'"><span class="' + files[num].cssClass + '">&nbsp;</span></a></td><td class="left docName" id="tdnd' + num +'">'  + (typeof(files[num].short_name) != 'undefined'?files[num].short_name:files[num].name) + '</td><td class="docInfo" id="tdrd' + num +'">' + files[num].size +'</td><td class="docInfo" id="tdth' + num +'">' + files[num].mtime +'</td></tr>').appendTo('#fileList');
		
				if(files[num].type== 'folder')
				{//this is foder item					
					enableFolderBrowsable(num);
				}else
				{//this is file item
					
					switch(files[num].cssClass)
					{
						case 'filePicture':							
							break;
						case 'fileFlash':
							break;
						case 'fileVideo':
							break;			
						case 'fileMusic':
							break;
						default:
						
							
					}
					enablePreview('#row' + num + ' td a', num);		
								
				}	
				enableContextMenu('#row' + num);
				enableShowDocInfo(num);										
				break;								
			
				
		}	
	
	
	
};

function enableShowDocInfo(num)
{

	$('#cb' + num).click(
		function()
		{

			setDocInfo('doc', num);
		}
	);	
};
/**
*	show up the selected document information
* @param   type root or doc
*/
function setDocInfo(type, num)
{
	

	var info = {};
	if(type == 'root')
	{
		info = currentFolder;
	}else
	{
		info = files[num];
	}
	

		if(info.type=="folder")
		{
			$('#folderPath').text(info.name);
			$('#folderFile').text(info.file);
			$('#folderSubdir').text(info.subdir);
			$('#folderCtime').text(info.ctime);
			$('#folderMtime').text(info.mtime);
			if(info.is_readable == '1')
			{
				$('#folderReadable').html("<span class=\"flagYes\">&nbsp;</span>");	
			}else
			{
				$('#folderReadable').html("<span class=\"flagNo\">&nbsp;</span>");
			}
			if(info.is_writable == '1')
			{
				$('#folderWritable').html("<span class=\"flagYes\">&nbsp;</span>");	
			}else
			{
				$('#folderWritable').html("<span class=\"flagNo\">&nbsp;</span>");
			}	
			$('#folderFieldSet').css('display', '');
			$('#fileFieldSet').css('display', 'none');
		}else
		{
	

			$('#fileName').text(info.name);
			$('#fileSize').text(info.size);
			$('#fileType').text(info.fileType);
			$('#fileCtime').text(info.ctime);
			$('#fileMtime').text(info.mtime);
			if(info.is_readable == '1')
			{
				$('#fileReadable').html("<span class=\"flagYes\">&nbsp;</span>");	
			}else
			{
				$('#fileReadable').html("<span class=\"flagNo\">&nbsp;</span>");
			}
			if(info.is_writable == '1')
			{
				$('#fileWritable').html("<span class=\"flagYes\">&nbsp;</span>");	
			}else
			{
				$('#fileWritable').html("<span class=\"flagNo\">&nbsp;</span>");
			}	
			$('#folderFieldSet').css('display', 'none');
			$('#fileFieldSet').css('display', '');
		   if(typeof(selectFile) != 'undefined')
		   {
		   	$('#selectCurrentUrl').unbind('click').click( 
		   		function()
		   		{
		   			
		   			selectFile(info.url);
		   		}
		   	);
		   	$('#returnCurrentUrl').show();
		   	 
		   }else
		   {
		   	$('#returnCurrentUrl').hide();
		   }
		   	
			
		}
		

		
	
	
};
		function search()
		{
			searchRequired = true;			
			var url = getUrl('view', true, true, true);		

		$('#rightCol').empty();
		ajaxStart('#rightCol');		
		
		$('#rightCol').load(url, 
					{},
					function(){
							ajaxStop('#rightCol img.ajaxLoadingImg');
							initAfterListingLoaded();
						});			
			return false;
		};
		
		function closeWinPlay()
		{
			tb_remove();
			$('#playGround').empty();
		};
		
		function closeWindow(msg)
		{
			
			if(window.confirm(msg))
			{
				window.close();
			}else
			{
				return false;
			}

			
		};