			function save(id, text)
			{
				jQuery('#text').val(text);
				jQuery('#save_as_request').val('0');
				jQuery('#name').val(currentName);					
				jQuery('#folder').val(currentFolder);				
				do_save(false);
			};
			function do_save(saveAsRequest)
			{
				jQuery('#windowProcessing').jqmShow();

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

											if(typeof(saveAsRequest) == 'boolean' && saveAsRequest)
											{
												jQuery('#windowSaveAs').jqmShow();		
											}											
										}
										else if(data.error != '')
										{
											alert(data.error);
											jQuery('#windowProcessing').jqmHide();											
											if(typeof(saveAsRequest) == 'boolean' && saveAsRequest)
											{
												jQuery('#windowSaveAs').jqmShow();		
											}
										}else
										{													
											jQuery('#windowProcessing').jqmHide();
											jQuery('#windowSaveAs').jqmHide();
											currentFolder = data.folder;
											currentName = data.name;
										}
				} 
			}; 
			jQuery('#frmProcessing').ajaxSubmit(options); 		
			};
			function save_as(id, text)
			{
				
				jQuery('#text').val(text);
				jQuery('#windowSaveAs').jqmShow();
				var saveTo = jQuery('#save_to');
				jQuery(saveTo).removeOption(/./);
				jQuery(saveTo).ajaxAddOption(urlGetFolderList, {}, false, 
																	function()
																	{
																			jQuery(saveTo).selectOptions(currentFolder);
																		});				
	
			  
			};
			function do_save_as()
			{
				var pattern=/^[A-Za-z0-9_ \-]+$/i;				
				var newName = jQuery('#new_name');				
				var saveAs = jQuery('#save_to').get(0);
				var ext = jQuery('#ext').get(0);
				if(!pattern.test(jQuery(newName).val()))
				{
					alert(warningInvalidName);	
				}else if(saveAs.selectedIndex < 0)
				{
					alert(waringFolderNotSelected);
				}else if(ext.selectedIndex < 0)
				{
					alert(warningExtNotSelected);
				}
				else
				{			
					
					jQuery('#name').val(jQuery(newName).val() + "." + ext.options[ext.selectedIndex].value);					
					jQuery('#folder').val(saveAs.options[saveAs.selectedIndex].value);
					jQuery('#save_as_request').val('1');
					jQuery('#windowSaveAs').jqmHide();	
					do_save(true);
				}
				return false;						
			};