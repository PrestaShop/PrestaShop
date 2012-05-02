/*
	* author: Logan Cai
	* Email: cailongqun [at] yahoo [dot] com [dot] cn
	* Website: www.phpletter.com
	* Created At: 21/April/2007
*/

	/**
	*	get current selected mode value
	*/
	function getModeValue()
	{
		//check if an mode has been selected or selected first one be default
		var CheckedElem = null;
		for(var i = 0; i < document.formAction.mode.length; i++)
		{
			if(document.formAction.mode[i].checked || i == 0)
			{
				CheckedElem = document.formAction.mode[i];
			}
		}
		CheckedElem.checked = true;	
		return CheckedElem.value;
	};
	/**
	*	get fired when mode changed
	*	fire according function
	*/
	function changeMode(restore, force)
	{
		
			var mode = getModeValue();
			var imageMode = $('#image_mode');
			if(mode != $(imageMode).val() || (typeof(restore) == "boolean"))
			{
				/**
				* confirm it when there has been some changes before go further
				*/
				if(isImageHistoryExist() || typeof(force) == 'boolean')
				{
					if(typeof(restore) == "boolean" || typeof(force) == 'boolean' )
					{
						if(!restoreToOriginal(restore))
						{
							return false;
							
						}
						clearImageHistory();
					}
					else if(!window.confirm(warningLostChanges))
					{
						cancelChangeMode();
						return false;
					}else
					{
						restoreToOriginal(false);						
						clearImageHistory();
					}				
				}else if((typeof(restore) == "boolean" && restore))
				{
					return false;	
				}
				initPositionHandler();				
				switch(mode)
				{
					case "resize":
						switch($('#image_mode').val())
						{
							case "crop":
								disableCrop();								
								break;
							case "rotate":
								disableRotate();
								break;
							case "flip":
								disableFlip();
								break;								
							default:
								disableRotate();
						}						
						enableResize(document.formAction.constraint.checked);
						break;
					case "crop":						
						switch($('#image_mode').val())
						{
							case "resize":
								disableResize();								
								break;
							case "rotate":
								disableRotate();
								break;
							case "flip":
								disableFlip();
								break;								
							default:
								disableRotate();
						}	
						enableCrop();
						
						break;
					case "rotate":
						switch($('#image_mode').val())
						{
							case "resize":
								disableResize();								
								break;
							case "crop":
								disableCrop();
								break;
							case "flip":
								disableFlip();
								break;
							default:
								//do nothing
						}						
						enableRotate();
						break;	
					case "flip":
						switch($('#image_mode').val())
						{
							case "resize":
								disableResize();								
								break;
							case "crop":
								disableCrop();
								break;
							case "rotate":
								disableRotate();
								break;
							default:
								//do nothing
						}								
						enableFlip();
						break;
					default:
						alert('Unexpected Operation!');
						return false;			
				}
				$('#image_mode').val(mode);
			}

	};
	
	
	function resetEditor()
	{
		if(isImageHistoryExist())
		{
			changeMode(true);
		}else
		{
			alert(warningResetEmpty);
		}
		return false;
		
	};

	/**
	*	enable to crop function
	*/
	function enableCrop()
	{
		var widthField = $('#width');
        var heightField = $('#height');
        var topField = $('#y');
        var leftField = $('#x');
        var imageToResize = getImageElement();
        var imageWidth = $(imageToResize).attr('width');
        var imageHeight = $(imageToResize).attr('height');

        var overlay = $('#resizeMe');
        var imageContainer = $('#imageContainer');
		var imageContainerTop = parseInt($(imageContainer).css('top').replace('px', ''));
		var imageContainerLeft = parseInt($(imageContainer).css('left').replace('px', ''));
        //Init Container
        $(imageContainer).css('width', imageWidth + 'px');
        $(imageContainer).css('height', imageHeight + 'px');
        $(imageToResize).css('opacity', '.5');
		
        //Init Overlay
        overlay.css('background-image', 'url('+ $(imageToResize).attr('src')+')');
        overlay.css('width', imageWidth + 'px');
        overlay.css('height', imageHeight + 'px');

        //Init Form
        widthField.val(imageWidth);
        heightField.val(imageHeight);
        topField.val(0);
        leftField.val(0);		
	    $(overlay).Resizable(
			{
				minWidth: 10,
				minHeight: 10,
				maxWidth: imageWidth,
				maxHeight: imageHeight,
				minTop: imageContainerTop,
				minLeft: imageContainerLeft,
				maxRight: (parseInt(imageWidth) + imageContainerLeft),
				maxBottom: (parseInt(imageHeight) + imageContainerTop),
				dragHandle: true,
				onDrag: function(x, y)
				{
					this.style.backgroundPosition = '-' + (x - imageContainerLeft) + 'px -' + (y - imageContainerTop) + 'px';
					$(topField).val(Math.round(y - imageContainerTop));
					$(leftField).val(Math.round(x - imageContainerLeft));
					addImageHistory();
				},
				handlers: {
					se: '#resizeSE',
					e: '#resizeE',
					ne: '#resizeNE',
					n: '#resizeN',
					nw: '#resizeNW',
					w: '#resizeW',
					sw: '#resizeSW',
					s: '#resizeS'
				},
				onResize : function(size, position) {
					this.style.backgroundPosition = '-' + (position.left - imageContainerLeft) + 'px -' + (position.top - imageContainerTop) + 'px';
					$(widthField).val(Math.round(size.width));
					$(heightField).val(Math.round(size.height));
					$(topField).val(Math.round(position.top - imageContainerTop));
					$(leftField).val(Math.round(position.left - imageContainerLeft));
					addImageHistory();
					$('#ratio').val($(overlay).ResizableRatio() );	
				}
			}
		);
		enableConstraint();
		toggleConstraint();
		disableRotate();
		
	};
	/*
	*	disable crop function 
	*/	
	function disableCrop()
	{
		$('#resizeMe').ResizableDestroy();		
		hideHandlers();					
	};
	/**
	*	disable resize function
	*/
	function disableResize()
	{
		$('#resizeMe').ResizableDestroy();
		
		hideHandlers();					
	};
	/**
	*	hide all handlers 
	*/	
	function hideHandlers()
	{
		$('#resizeSE').hide();
		$('#resizeE').hide();
		$('#resizeNE').hide();	
		$('#resizeN').hide();
		$('#resizeNW').hide();
		$('#resizeW').hide();	
		$('#resizeSW').hide();
		$('#resizeS').hide();	
	};
	/**
	*
	*	enable to resize the image
	*/
	function enableResize(constraint)
	{
		hideHandlers();
		var imageToResize = getImageElement();	
		var imageContainer = $('#imageContainer');
		var imageContainerTop = parseInt($(imageContainer).css('top').replace('px', ''));
		var imageContainerLeft = parseInt($(imageContainer).css('left').replace('px', ''));		
		var resizeMe = $('#resizeMe');
		var width = $('#width');
		var height = $('#height');
		//ensure the container has same size with the image
		$(imageContainer).css('width', $(imageToResize).attr('width') + 'px');
		$(imageContainer).css('height', $(imageToResize).attr('height') + 'px');
		$(resizeMe).css('width', $(imageToResize).attr('width') + 'px');
		$(resizeMe).css('height', $(imageToResize).attr('height') + 'px');
		$('#width').val($(imageToResize).attr('width'));
		$('#height').val($(imageToResize).attr('height'));
		$('#x').val(0);
		$('#y').val(0);		
		$(resizeMe).Resizable(
			{
				minWidth: 10,
				minHeight: 10,
				maxWidth: 2000,
				maxHeight: 2000,
				minTop: imageContainerTop,
				minLeft: imageContainerLeft,
				maxRight: 2000,
				maxBottom: 2000,
				handlers: {
					s: '#resizeS',
					se: '#resizeSE',
					e: '#resizeE'
				},
				onResize: function(size)
				{
					$(imageToResize).attr('height', Math.round(size.height).toString());
					$(imageToResize).attr('width', Math.round(size.width).toString());
					$(width).val(Math.round(size.width));
					$(height).val(Math.round(size.height));		
					$(imageContainer).css('width', $(imageToResize).attr('width') + 'px');
					$(imageContainer).css('height', $(imageToResize).attr('height') + 'px');	
					$('#ratio').val($(resizeMe).ResizableRatio() );			
					addImageHistory();				
				}

			}
		);	
		$(resizeMe).ResizeConstraint(constraint);
		if(typeof(constraint) == 'boolean' && constraint)
		{		
			$('#resizeS').hide();
			$('#resizeE').hide();			
		}else
		{			
			$('#resizeS').show();
			$('#resizeE').show();
		}		
		$('#resizeSE').show();
		$('#ratio').val($(resizeMe).ResizableRatio() );	
			
			
	};
	/**
	*	initiate the position of handler
	*/
	function initPositionHandler()
	{
		var widthField = $('#width');
        var heightField = $('#height');
        var topField = $('#x');
        var leftField = $('#y');

        var imageToResize = getImageElement();
        var imageWidth = $(imageToResize).attr('width');
        var imageHeight = $(imageToResize).attr('height');

        var overlay = $('#resizeMe');
        var imageContainer = $('#imageContainer');
		var imageContainerTop = parseInt($(imageContainer).css('top').replace('px', ''));
		var imageContainerLeft = parseInt($(imageContainer).css('left').replace('px', ''));
        //Init Container
        $(imageContainer).css('width', imageWidth + 'px');
        $(imageContainer).css('height', imageHeight + 'px');
		
        //Init Overlay
		$(imageToResize).css('opacity', '100');
        $(overlay).css('width', imageWidth + 'px');
        $(overlay).css('height', imageHeight + 'px');
		$(overlay).css('background-image', '');
		$(overlay).css('backgroundPosition', '0 0');
		$(overlay).css('left', imageContainerLeft);
		$(overlay).css('top', imageContainerTop);

        //Init Form
        $(widthField).val(imageWidth);
        $(heightField).val(imageHeight);
        $(topField).val(0);
        $(leftField).val(0);
		$('#angle').val(0);
		$('#flip_angle').val('');
	};
	/**
	*	enable rotate function
	*/
	function enableRotate()
	{
		hideHandlers();
	toggleDisabledButton('actionRotateLeft', false);
	toggleDisabledButton('actionRotateRight', false);			
		
	};
	/**
	*	disable rotation function
	*/
	function disableRotate()
	{
	toggleDisabledButton('actionRotateLeft', true);
	toggleDisabledButton('actionRotateRight', true);			
	};
	
	function enableConstraint()
	{
		$('#constraint').removeAttr('disabled');
	};
	
	function disableConstraint()
	{
		$('#constraint').attr('disabled', true);
	};
	function ShowHandlers()
	{
		$('#resizeSE').show();
		$('#resizeE').show();
		$('#resizeNE').show();	
		$('#resizeN').show();
		$('#resizeNW').show();
		$('#resizeW').show();	
		$('#resizeSW').show();
		$('#resizeS').show();	
	}	;
	
	/**
	*	turn constraint on or off
	*/
	function toggleConstraint()
	{
		hideHandlers();	
		if(document.formAction.constraint.checked)
		{
			$('#resizeMe').ResizeConstraint(true);
			switch(getModeValue())
			{
				case "resize":
					$('#resizeSE').show();	
					break;
				case "crop":
					$('#resizeSE').show();
					$('#resizeNE').show();	
					$('#resizeNW').show();
					$('#resizeSW').show();
					
					break;
				case "rotate":
					break;
			}
						
		}else
		{
			$('#resizeMe').ResizeConstraint(false);
			switch(getModeValue())
			{
				case "resize":
					$('#resizeSE').show();	
					$('#resizeE').show();
					$('#resizeS').show();
					break;
				case "crop":
					ShowHandlers();
					break;
				case "rotate":
					break;					
			}			
		}
		
	};
	

	/**
	*	restore to the state the image was
	*/
	function restoreToOriginal(warning)
	{
			if(typeof(warning) == "boolean" && warning)
			{
					if(!window.confirm(warningReset))
					{
						return false;	
					}
			}
			
			$("#imageContainer").empty();
			$("#hiddenImage img").clone().appendTo("#imageContainer");		
			return true;
			
	
	};
	/*
	*	left rotate
	*/	
	function leftRotate()
	{
		
		var imageToResize = getImageElement();
		$(imageToResize).rotate(-90);		
		swapWidthWithHeight();
		addImageHistory();
		var angle = $('#angle');

		var angleDegree = (parseInt($(angle).val()) + 90);
		angleDegree = ((angleDegree == 360)?angleDegree:angleDegree%360);
		$(angle).val((angleDegree )); 
		return false;
		
	};
	/**
	*	cancel mode change
	*/
	function cancelChangeMode()
	{
		$('#formAction input[@value=' + $('#image_mode').val() + ']').attr('checked', true);
	};
	/**
	*	get the image element which is going to be modified
	*/
	function getImageElement()
	{
		var imageElement = null;
		var imageContainer = document.getElementById('imageContainer');
		for(var i = 0; i < imageContainer.childNodes.length; i++)
		{
			if((typeof(imageContainer.childNodes[i].name) != "undefined" && imageContainer.childNodes[i].name.toLowerCase() == 'image') || (typeof(imageContainer.childNodes[i].tagName) != "undefined" && (imageContainer.childNodes[i].tagName.toLowerCase() == 'canvas' ||  imageContainer.childNodes[i].tagName.toLowerCase() == 'img'))  )
			{
				imageElement = 	imageContainer.childNodes[i];
			}
		}
		return imageElement;
	};
	/*	
		right rotate
	*/
	function rightRotate()
	{
		
		var imageToResize = getImageElement();
		$(imageToResize).rotate(90);	
		swapWidthWithHeight();
		addImageHistory();
		var angle = $('#angle');

		
		var angleDegree = (parseInt($(angle).val()) - 90 );
		if(angleDegree < 0)
		{
			angleDegree += 360;
		}
		angleDegree = ((angleDegree == 360)?angleDegree:angleDegree%360);		
		$(angle).val((angleDegree )); 
		return false;
	}	;
	/**
	*	swap image width with height when rotation fired
	*/
	function swapWidthWithHeight()
	{
		var imageContainer = $('#imageContainer');
		var resizeMe = $('#resizeMe');
		var width = $('#width');
		var height = $('#height');			
		var imageToResize = getImageElement();
		var newWidth = 0;
		var newHeight = 0;
		newWidth = $(imageToResize).attr('width');
		newHeight = $(imageToResize).attr('height');			
		$(imageContainer).css('width', newWidth + 'px');
		$(imageContainer).css('height', newHeight + 'px');		
		$(width).val(newWidth);
		$(height).val(newHeight);	
		$(resizeMe).css('width', newWidth + 'px');
		$(resizeMe).css('height', newHeight + 'px');
		


				
	};



	/**
	*	records all change mede to the image
	*	this features will be implemented next release
	*/
	function addImageHistory()
	{
		imageHistory = true;
		initDisabledButtons(false);
	};
	
	
	/**
	*	cleare all records
	*	this features will be implemented next release
	*/	
	function clearImageHistory()
	{
		imageHistory = false;
		initDisabledButtons(true);
		
		
	};
	
	function initDisabledButtons(forceDisable)
	{		
		if(numSessionHistory)
		{
			toggleDisabledButton('actionUndo', false);
		}else
		{
			toggleDisabledButton('actionUndo', true);
		}
		if(imageHistory)
		{
			toggleDisabledButton('actionSave', false);	
			toggleDisabledButton('actionReset', false);
		}else
		{
			toggleDisabledButton('actionSave', true);	
			toggleDisabledButton('actionReset', true);
		}
	};
	
	/**
	*	return record
	*	this features will be implemented next release
	*/		
	function getImageHistory()
	{
		return imageHistory;
	};
	/**
	*	check if there exists any changes
	*	this features will be implemented next release
	*/		
	function isImageHistoryExist()
	{
		return imageHistory;		
	};
	
function flipHorizontal()
{
	if(window.confirm(warningFlipHorizotal))
	{
		addImageHistory();
		$('#flip_angle').val('horizontal');	
		$('#mode').val('flip');
		saveImage();		
	}

	return false;
};

function flipVertical()
{
	if(window.confirm(warningFlipVertical))
	{
	addImageHistory();
	$('#flip_angle').val('vertical');	
	$('#mode').val('flip');
	saveImage();		
	}

	return false;
};

function enableFlip()
{
	toggleDisabledButton('actionFlipH', false);
	toggleDisabledButton('actionFlipV', false);
};

function toggleDisabledButton(buttonId, forceDisable)
{
	var disabledButton = $('#' + buttonId);
	var newClass = '';
	var changeRequired = true;
	var toBeDisabled = false;
	var currentClass = $(disabledButton).attr('class') ;
	if(typeof(forceDisable) == 'boolean')
	{
		
		if(forceDisable && currentClass == 'button')
		{
			newClass = 'disabledButton';
			$(disabledButton).attr('disabled', true);
		}else if(!forceDisable && currentClass == 'disabledButton')
		{
			newClass = 'button';
			$(disabledButton).removeAttr('disabled');					
		}else
		{
			changeRequired = false;
		}
		
		
	}
	else if(currentClass == 'button')
	{
		newClass = 'disabledButton';
		$(disabledButton).attr('disabled', true);
	}else
	{
		newClass = 'button';
		$(disabledButton).removeAttr('disabled');			
	}
	if(changeRequired)
	{
	$(disabledButton).removeClass('button disabledButton');
	$(disabledButton).addClass(newClass);			
	}

	
};

function disableFlip()
{
	toggleDisabledButton('actionFlipH', true);
	toggleDisabledButton('actionFlipV', true);	
};
	
	function undoImage()
	{
		if(numSessionHistory < 1)
		{
			alert(warningResetEmpty);

		}else
		{
			if(window.confirm(warningUndoImage))
			{
				processImage('formAction');
			}
			
		}
		return false;
		
	};	
	
function processImage(formId)
{
			$("#loading")
			   .ajaxStart(function(){
				   $(this).show();
			   })
			   .ajaxComplete(function(){
				   $(this).hide();
			   });	
			var options = 
			{ 
				dataType: 'json',
				error: function (data, status, e) 
				{
					alert(e);
				},				
				success: function(data) 
				{ 
					if(typeof(data.error) == 'undefined')
					{
						alert('Unexpected information ');
					}
					else if(data.error != '')
					{
						
						alert(data.error);
					}else
					{
						$("#loading").show();
						
								currentFolder = data.folder_path;
								if(data.save_as == '1')
								{
									numSessionHistory = 0;
								}else
								{
									numSessionHistory = parseInt(data.history);
								}
								$('#file_path').val(data.path);
								$('#path').val(data.path);
				        var preImage = new Image();
								preImage.width = data.width;
								preImage.height = data.height;													
				        preImage.onload = function()
				        {				
				          
									$('#hiddenImage').empty();														
									$(preImage).appendTo('#hiddenImage');
									
									changeMode(false, true);		
									$('#loading').hide(); 		
									$('#windowSaveAs').jqm({modal: true}).jqmHide();
				             
				        };
						var now = new Date();
				        preImage.src = data.path + "?" + now.getTime();

								
										
						
					}
				} 
			}; 
			$('#' + formId).ajaxSubmit(options); 		
			return false;
};

function saveAsImagePre()
{
	$('#windowSaveAs').jqm({modal: true}).jqmShow();
	var saveTo = $('#save_to');
	$(saveTo).removeOption(/./);
	$(saveTo).ajaxAddOption(urlGetFolderList, {}, false, 
														function()
														{
																$(saveTo).selectOptions(currentFolder);
															});
	return false;
};

function saveAsImage()
{

	var pattern=/^[A-Za-z0-9_ \-]+$/i;
	
	var newName = $('#new_name');
	
	var saveAs = $('#save_to').get(0);
	//alert($(saveAs).val());
	if(!pattern.test($(newName).val()))
	{
		alert(warningInvalidNewName);	
	}else if(saveAs.selectedIndex < 0)
	{
		alert(warningNoFolderSelected);
	}else
	{	
	
		$('#hidden_new_name').val($(newName).val());
		$('#hidden_save_to').val(saveAs.options[saveAs.selectedIndex].value);
		if(saveImage(true))
		{
			
			
		}
		
		
	}
	
	
	return false;
};

function saveImage(saveAs)
{
	if(typeof(saveAs) == 'boolean' && saveAs)
	{
		
	}else
	{//remove new name if just normal save
		$('#hidden_new_name').val('');
		$('#hidden_save_to').val('');
	}
	if (!isImageHistoryExist() && (typeof(saveAs) == 'undefined' || !saveAs))
	{
		alert(noChangeMadeBeforeSave);
	}else
	{
		
		if(processImage('formImageInfo'))
		{			
			return true;
		}
	}
	return false;
	
};



function editorClose()
{
	if(window.confirm(warningEditorClose))
	{
		window.close();
	}
	return false;
};
