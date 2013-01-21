<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	/**
	 * file manager platform
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	//$session->gc();
	require_once(CLASS_SESSION_ACTION);
	$sessionAction = new SessionAction();	
	if(CONFIG_LOAD_DOC_LATTER)
	{
		$fileList = array();
		$folderInfo = array('path'=>getCurrentFolderPath());
	}else 
	{
		require_once(CLASS_MANAGER);

	
		$manager = new manager();
		$manager->setSessionAction($sessionAction);
		$fileList = $manager->getFileList();
		$folderInfo = $manager->getFolderInfo();		
	}
	if(CONFIG_SYS_THUMBNAIL_VIEW_ENABLE)
	{
	$views = array(
		'detail'=>LBL_BTN_VIEW_DETAILS,
		'thumbnail'=>LBL_BTN_VIEW_THUMBNAIL,
	);		
	}else 
	{
	$views = array(
		'detail'=>LBL_BTN_VIEW_DETAILS,
	);		
	}

	if(!empty($_GET['view']))
	{
		switch($_GET['view'])
		{
			case 'detail':
			case 'thumbnail':
				$view = $_GET['view'];
				break;
			default:
				$view = CONFIG_DEFAULT_VIEW;
		}
	}else 
	{
		$view = CONFIG_DEFAULT_VIEW;
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" debug="true">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ajax File Manager</title>
<script type="text/javascript" src="jscripts/ajaxfilemanager_c.js"></script>
<!--<script type="text/javascript" src="jscripts/jquery.js"></script>
<script type="text/javascript" src="jscripts/form.js"></script>
<script type="text/javascript" src="jscripts/select.js"></script>
<script type="text/javascript" src="jscripts/thickbox.js"></script>
<script type="text/javascript" src="jscripts/calendar.js"></script>
<script type="text/javascript" src="jscripts/contextmenu.js"></script>
<script type="text/javascript" src="jscripts/media.js"></script>
<script type="text/javascript" src="jscripts/ajaxfileupload.js"></script>
<script type="text/javascript" src="jscripts/ajaxfilemanager.js"></script>-->
<script type="text/javascript">
	var globalSettings = {'upload_init':false};
	var queryString = '<?php echo Tools::safeOutput(makeQueryString(array('path'))); ?>';
	var paths = {'root':'<?php echo addTrailingSlash(backslashToSlash(CONFIG_SYS_ROOT_PATH)); ?>', 'root_title':'<?php echo LBL_FOLDER_ROOT; ?>'};
	var parentFolder = {};
	var urls = {
			'upload':'<?php echo CONFIG_URL_UPLOAD; ?>',
			'preview':'<?php echo CONFIG_URL_PREVIEW; ?>',
			'cut':'<?php echo CONFIG_URL_CUT; ?>',
			'copy':'<?php echo CONFIG_URL_COPY; ?>',
			'paste':'<?php echo CONFIG_URL_FILE_PASTE; ?>',
			'delete':'<?php echo CONFIG_URL_DELETE; ?>',
			'rename':'<?php echo CONFIG_URL_SAVE_NAME; ?>',
			'thumbnail':'<?php echo CONFIG_URL_IMG_THUMBNAIL;  ?>',
			'create_folder':'<?php echo CONFIG_URL_CREATE_FOLDER; ?>',
			'text_editor':'<?php echo  CONFIG_URL_TEXT_EDITOR; ?>',
			'image_editor':'<?php echo  CONFIG_URL_IMAGE_EDITOR; ?>',
			'download':'<?php echo CONFIG_URL_DOWNLOAD; ?>',
			'present':'<?php echo Tools::safeOutput(getCurrentUrl()); ?>',
			'home':'<?php echo CONFIG_URL_HOME; ?>',
			'view':'<?php echo CONFIG_URL_LIST_LISTING; ?>'			
		};
	var permits = {'del':<?php echo (CONFIG_OPTIONS_DELETE?1:0); ?>, 'cut':<?php echo (CONFIG_OPTIONS_CUT?'1':'0'); ?>, 'copy':<?php echo (CONFIG_OPTIONS_COPY?1:0); ?>, 'newfolder':<?php echo (CONFIG_OPTIONS_NEWFOLDER?1:0); ?>, 'rename':<?php echo (CONFIG_OPTIONS_RENAME?1:0); ?>, 'upload':<?php echo (CONFIG_OPTIONS_UPLOAD?1:0); ?>, 'edit':<?php echo (CONFIG_OPTIONS_EDITABLE?1:0); ?>, 'view_only':<?php echo (CONFIG_SYS_VIEW_ONLY?1:0); ?>};
	var currentFolder = {};
	var warningDelete = '<?php echo WARNING_DELETE; ?>';
	var newFile = {'num':1, 'label':'<?php echo FILE_LABEL_SELECT; ?>', 'upload':'<?php echo FILE_LBL_UPLOAD; ?>'};
	var counts = {'new_file':1};
	var thickbox = {'width':'<?php echo CONFIG_THICKBOX_MAX_WIDTH; ?>', 
									'height':'<?php echo CONFIG_THICKBOX_MAX_HEIGHT; ?>',
									'next':'<?php echo THICKBOX_NEXT; ?>',
									'previous':'<?php echo THICKBOX_PREVIOUS; ?>',
									'close':'<?php echo THICKBOX_CLOSE; ?>' 
		
	};
	
	var tb_pathToImage = "theme/<?php echo CONFIG_THEME_NAME; ?>/images/loadingAnimation.gif";
	var msgInvalidFolderName = '<?php echo ERR_FOLDER_FORMAT; ?>';
	var msgInvalidFileName = '<?php echo ERR_FILE_NAME_FORMAT; ?>';
	var msgInvalidExt = '<?php echo ERR_FILE_TYPE_NOT_ALLOWED; ?>';
	var msgNotPreview = '<?php echo PREVIEW_NOT_PREVIEW; ?>';

	var warningCutPaste = '<?php echo WARNING_CUT_PASTE; ?>';
	var warningCopyPaste = '<?php echo WARNING_COPY_PASTE; ?>';
	var warningDel = '<?php echo WARNING_DELETE; ?>';
	var warningNotDocSelected = '<?php echo ERR_NOT_DOC_SELECTED; ?>';
	var noFileSelected = '<?php echo ERR_NOT_FILE_SELECTED; ?>';
    var unselectAllText = '<?php echo TIP_UNSELECT_ALL; ?>';
    var selectAllText = '<?php echo TIP_SELECT_ALL; ?>';
	var action = '<?php echo $sessionAction->getAction(); ?>';
	var numFiles = <?php echo $sessionAction->count(); ?>;
	var warningCloseWindow = '<?php echo WARING_WINDOW_CLOSE; ?>';
	var numRows = 0; 

	var wordCloseWindow = '<?php echo LBL_ACTION_CLOSE; ?>';
	var wordPreviewClick = '<?php echo LBL_CLICK_PREVIEW; ?>';

	var searchRequired = false;
	var supporedPreviewExts = '<?php echo CONFIG_VIEWABLE_VALID_EXTS; ?>'; 
	var supportedUploadExts = '<?php echo CONFIG_UPLOAD_VALID_EXTS; ?>'
	var elementId = <?php  echo (!empty($_GET['elementId'])?"'" . Tools::safeOutput($_GET['elementId']) . "'":'null'); ?>;
	var files = {};
$(document).ready(
	function()
	{
		jQuery(document).bind('keypress', function(event) {
			var code=event.charCode || event.keyCode;
			if(code && code == 13) {// if enter is pressed
	  			event.preventDefault(); //prevent browser from following the actual href
			};
		});		
		if(typeof(cancelSelectFile) != 'undefined')
		{
			$('#linkClose').show();
		}
		$('input[@name=view]').each(
			function()
			{

				if(this.value == '<?php echo $view; ?>')
				{
					this.checked = true;
				}else
				{
					this.checked = false;
				}
			}
		);
		
		popUpCal.clearText = '<?php echo CALENDAR_CLEAR; ?>';
		popUpCal.closeText = '<?php echo CALENDAR_CLOSE; ?>';
		popUpCal.prevText = '<?php echo CALENDAR_PREVIOUS; ?>';
		popUpCal.nextText = '<?php echo CALENDAR_NEXT; ?>';
		popUpCal.currentText = '<?php echo CALENDAR_CURRENT; ?>';
		popUpCal.buttonImageOnly = true;
		popUpCal.dayNames = new Array('<?php echo CALENDAR_SUN; ?>','<?php echo CALENDAR_MON; ?>','<?php echo CALENDAR_TUE; ?>','<?php echo CALENDAR_WED; ?>','<?php echo CALENDAR_THU; ?>','<?php echo CALENDAR_FRI; ?>','<?php echo CALENDAR_SAT; ?>');
		popUpCal.monthNames = new Array('<?php echo CALENDAR_JAN; ?>','<?php echo CALENDAR_FEB; ?>','<?php echo CALENDAR_MAR; ?>','<?php echo CALENDAR_APR; ?>','<?php echo CALENDAR_MAY; ?>','<?php echo CALENDAR_JUN; ?>','<?php echo CALENDAR_JUL; ?>','<?php echo CALENDAR_AUG; ?>','<?php echo CALENDAR_SEP; ?>','<?php echo CALENDAR_OCT; ?>','<?php echo CALENDAR_NOV; ?>','<?php echo CALENDAR_DEC; ?>');
		popUpCal.dateFormat = 'YMD-';
		$('.inputMtime').calendar({autoPopUp:'both', buttonImage:'theme/<?php echo CONFIG_THEME_NAME; ?>/images/standard/date_picker.png'});
		
		
		initAfterListingLoaded();
		//addMoreFile();

	} );

	
</script>
<?php
	if(file_exists(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'jscripts' . DIRECTORY_SEPARATOR . 'for_' . CONFIG_EDITOR_NAME . ".js")
	{
	?>
	<script type="text/javascript" src="jscripts/<?php echo 'for_' . CONFIG_EDITOR_NAME . '.js'; ?>"></script>
	<?php
	}
?>
<link rel="stylesheet" type="text/css" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/template.css" />
<link rel="stylesheet" type="text/css" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/jquery-calendar.css" />
<link rel="stylesheet" href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/thickbox.css" type="text/css" media="screen" />
<!--[if IE 6]>
<link href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/ie6.css" type="text/css" rel="Stylesheet" media="screen">
<![endif]-->
</head>
<body style="background-color:#E8E8E8;">
	<div id="wrapper">
  	<div id="header">
  		<dl id="currentFolderInfo">
  			<dt><?php echo LBL_CURRENT_FOLDER_PATH; ?></dt>
  			<dd id="currentFolderPath"><?php echo $folderInfo['path']; ?></dd>
  		</dl>
    	<div id="viewList">
    		
    	
    			<label><?php echo LBL_BTN_VIEW_OPTIONS; ?></label>
					<?php 
						foreach($views as $k=>$v)
						{
							?>
							<input type="radio" name="view"  class="radio" onclick="changeView(this);" value="<?php echo $k; ?>" <?php echo ($k==$view?'checked':''); ?>> <?php echo $v; ?> &nbsp;&nbsp;
							
							<?php
						}
					?></div>
				<ul id="actionHeader">
					<li><a href="#" id="actionRefresh" onclick="return windowRefresh();"><span><?php echo LBL_ACTION_REFRESH; ?></span></a></li>
					<li><a href="#" id="actionSelectAll" class="check_all" onclick="return checkAll(this);"><span><?php echo LBL_ACTION_SELECT_ALL; ?></span></a></li>
					<?php 
						if(CONFIG_OPTIONS_DELETE)
						{
							?>
							<li><a href="#" id="actionDelete" onclick="return deleteDocuments();"><span><?php echo LBL_ACTION_DELETE; ?></span></a></li>
							<?php
						}
					?>
					<?php 
						if(CONFIG_OPTIONS_CUT)
						{
							?>
							<li><a href="#" id="actionCut" onclick="return cutDocuments('<?php echo ERR_NOT_DOC_SELECTED_FOR_CUT; ?>');"><span><?php echo LBL_ACTION_CUT; ?></span></a></li>			
							<?php
						}
					?>
					<?php 
						if(CONFIG_OPTIONS_COPY)
						{
							?>
							<li><a href="#" id="actionCopy" onclick="return copyDocuments('<?php echo ERR_NOT_DOC_SELECTED_FOR_COPY; ?>');"><span><?php echo LBL_ACTION_COPY; ?></span></a></li>
							<?php
						}
					?>
					<?php 
						if(CONFIG_OPTIONS_CUT || CONFIG_OPTIONS_COPY)
						{
							?>
							<li><a href="#" id="actionPaste" onclick="return pasteDocuments('<?php echo ERR_NOT_DOC_SELECTED_FOR_PASTE; ?>');"><span><?php echo LBL_ACTION_PASTE; ?></span></a></li>
							<?php
						}
					?>															
					
					<?php 
						if(CONFIG_OPTIONS_NEWFOLDER)
						{
							?>
							<li><a  id="actionNewFolder" href="#" onclick="return newFolderWin(this);"><span><?php echo LBL_BTN_NEW_FOLDER; ?></span></a></li>
							<?php
						}
					?>
					<?php 
						if(CONFIG_OPTIONS_UPLOAD)
						{
							?>
							<li><a  id="actionUpload" href="#" onclick="return uploadFileWin(this);"><span><?php echo LBL_BTN_UPLOAD; ?></span></a></li>
							<?php
						}
					?>
																	
					
					
					
		
					
<!--					<li><a href="#" id="actionClose" onclick="closeWindow('<?php echo IMG_WARING_WIN_CLOSE; ?>');"><?php echo IMG_BTN_CLOSE; ?></a></li>-->
					<li><a href="#" class="thickbox" id="actionInfo" onclick="return infoWin(this);"><span>Info</span></a></li>
					<!-- thest functions will be added in the near future
 					<li ><a href="#" id="actionZip"><span>Zip</span></a><li>
					<li ><a href="#" id="actionUnzip"><span>Unzip</span></a><li>-->
				</ul>    
<form action="" method="post" name="formAction" id="formAction"><input type="hidden" name="currentFolderPath" id="currentFolderPathVal" value="" /><select name="selectedDoc[]" id="selectedDoc" style="display:none;" multiple="multiple"></select><input type="hidden" name="action_value" value="" id="action_value" /></form>				  
    </div>
    
    <div id="body">
         
         
      <div id="rightCol">
	      	<?php
			if(CONFIG_LOAD_DOC_LATTER )
			{
				$currentPath = getCurrentFolderPath();
				?>
				<script type="text/javascript">
				parentFolder = {path:'<?php echo getParentFolderPath($currentPath); ?>'}; 
				currentFolder = {'friendly_path':'<?php echo transformFilePath($currentPath); ?>'};
					$(document).ready(
						function()
						{
							var url = getUrl('view', false, false, false);
							$('#rightCol').empty();
							ajaxStart('#rightCol');		
							
							$('#rightCol').load(url, 
										{},
										function(){
												ajaxStop('#rightCol img.ajaxLoadingImg');
												urls.present = getUrl('home', true, true);
												initAfterListingLoaded();
											});
						}
					);
				</script>
				<?php
			}else 
			{
				include_once(CONFIG_URL_LIST_LISTING);
			}
	      	 ?>
      </div> 
      
      <div id="leftCol">
      


				<fieldset id="folderFieldSet" >
					
				<legend><?php echo LBL_FOLDER_INFO; ?></legend>
				<table cellpadding="0" cellspacing="0" class="tableSummary" id="folderInfo">
					<tbody>
						<tr>
							<th><?php echo LBL_FOLDER_PATH; ?></th>
							<td colspan="3" id="folderPath"><?php echo transformFilePath($folderInfo['path']); ?></td>
						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_CREATED; ?></th>
							<td colspan="3" id="folderCtime"><?php echo (!empty($folderInfo['ctime'])?date(DATE_TIME_FORMAT,$folderInfo['ctime']):'&nbsp;') ; ?></td>

						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_MODIFIED; ?></th>
							<td colspan="3" id="folderMtime"><?php echo (!empty($folderInfo['mtime'])?date(DATE_TIME_FORMAT,$folderInfo['mtime']):'&nbsp;'); ?></td>
						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_SUDDIR; ?></th>
							<td  colspan="3" id="folderSubdir"><?php echo (isset($folderInfo['subdir'])?$folderInfo['subdir']:"&nbsp;"); ?></td>

						</tr>
						<tr>
							<th><?php echo LBL_FOLDER_FIELS; ?></th>
							<td  colspan="3" id="folderFile"><?php echo (isset($folderInfo['file'])?$folderInfo['file']:'&nbsp;'); ?></td>						
						</tr>
						
						<tr>
							<th><?php echo LBL_FOLDER_WRITABLE; ?></th>
							<td id="folderWritable"><span class="<?php echo (isset($folderInfo['is_readable'])?($folderInfo['is_readable']?'flagYes':'flagNo'):'&nbsp;'); ?>">&nbsp;</span></td>
							<th><?php echo LBL_FOLDER_READABLE; ?></th>
							<td  id="folderReadable"><span class="<?php echo (isset($folderInfo['is_writable'])?($folderInfo['is_writable']?'flagYes':'flagNo'):'&nbsp;'); ?>">&nbsp;</span></td>						
						
						</tr>



					</tbody>
				</table>
				</fieldset>
			<fieldset id="fileFieldSet" style="display:none" >
				<legend><?php echo LBL_FILE_INFO; ?></legend>
				<table cellpadding="0" cellspacing="0" class="tableSummary" id="fileInfo">
					<tbody>
						<tr>
							<th><?php echo LBL_FILE_NAME; ?></th>
							<td colspan="3" id="fileName"></td>
						</tr>
						<tr>
							<th><?php echo LBL_FILE_CREATED; ?></th>
							<td colspan="3" id="fileCtime"></td>

						</tr>
						<tr>
							<th><?php echo LBL_FILE_MODIFIED; ?></th>
							<td colspan="3" id="fileMtime"></td>
						</tr>
						<tr>
							<th><?php echo LBL_FILE_SIZE; ?></th>
							<td  colspan="3" id="fileSize"></td>

						</tr>
						<tr>
							<th><?php echo LBL_FILE_TYPE; ?></th>
							<td  colspan="3" id="fileType"></td>						
						</tr>
						<tr>
							<th><?php echo LBL_FILE_WRITABLE; ?></th>
							<td id="fileWritable"><span class="flagYes">&nbsp;</span></td>
							<th><?php echo LBL_FILE_READABLE; ?></th>
							<td id="fileReadable"><span class="flagNo">&nbsp;</span></td>		
						</tr>

					</tbody>
				</table>
		
        <p class="searchButtons" id="returnCurrentUrl">
  
        	<span class="right" id="linkSelect">
        		<input type="button" value="<?php echo MENU_SELECT; ?>"  id="selectCurrentUrl" class="button">
        	</span>
        	
        </p>				
			</fieldset>
			
     
      
      	<fieldset class="boxSearch">
      		<legend><?php echo LBL_SEARCH; ?></legend>



          <table cellpadding="0" cellspacing="0" class="tableSearch">
          	<tbody>
	          <tr>
	          	<td>
	          		<b><?php echo LBL_SEARCH_NAME; ?></b> <br />
	            	<input type="text" class="input inputSearch" name="search_name" id="search_name" />
	          	</td>
	         </tr>
	          <tr>
	          	<td >
	          	<b><?php echo LBL_SEARCH_FOLDER; ?></b><br />
	          	<span id="searchFolderContainer">
	          	<?php
	          		if(CONFIG_LOAD_DOC_LATTER)
	          		{
	          			?>
	          			<script type="text/javascript">
	          				$(document).ready(
	          					function()
	          					{
	          						ajaxStart('#searchFolderContainer');		
	          						$('#searchFolderContainer').load('<?php echo CONFIG_URL_LOAD_FOLDERS; ?>');
	          					}
	          				);
	          			</script>
	          			<?php
	          		}else 
	          		{
	          	?>
		            <select class="input inputSearch" name="search_folder" id="search_folder">
		            	<?php 
		            		
										foreach(getFolderListing(CONFIG_SYS_ROOT_PATH) as $k=>$v)
										{
											?>
		                  <option value="<?php echo $v; ?>" <?php echo (removeTrailingSlash(backslashToSlash(($folderInfo['path']))) == removeTrailingSlash(backslashToSlash(($v)))?' selected="selected"':''); ?>><?php echo shortenFileName($k, 30); ?></option>
		                  <?php 
										}
		            		
									?>            	
		            </select>
		      <?php
	          		}
		      ?></span>
	          </td>
	         </tr>  
        		<tr>
        			<td>
        		<b><?php echo LBL_SEARCH_MTIME; ?></b><br />
        		<input type="text" class="input inputMtime" name="search_mtime_from" id="search_mtime_from" value="<?php echo (!empty($_GET['search_mtime_from'])?Tools::safeOutput($_GET['search_mtime_from']):''); ?>" /> 
        		<span class="leftToRightArrow">&nbsp;</span>
        		<input type="text" class="input inputMtime" name="search_mtime_to" id="search_mtime_to" value="<?php echo (!empty($_GET['search_mtime_to'])?Tools::safeOutput($_GET['search_mtime_to']):''); ?>" />
        	</td></tr>

			<tr>
				<td>
          	<b><?php echo LBL_SEARCH_RECURSIVELY; ?></b>&nbsp;&nbsp;
          	<input type="radio" name="search_recursively" value="1" id="search_recursively_1" class="radio" <?php echo (!empty($_GET['search_recursively'])?'checked="checked"':''); ?> /> <?php echo LBL_RECURSIVELY_YES; ?>
          	<input type="radio" name="search_recursively" value="0" id="search_recursively_0" class="radio" <?php echo (empty($_GET['search_recursively'])?'checked="checked"':''); ?> /> <?php echo LBL_RECURSIVELY_NO; ?>
          	</td>
          </tr>	                	
          	</tbody>
</table>



       	
        <p class="searchButtons">
        	<span class="left" id="linkClose" style="display:none">
        		<input  type="button" value="<?php echo LBL_ACTION_CLOSE; ?>" onclick="return cancelSelectFile();"  class="button">
        	</span>
        	<span class="right" id="linkSearch">
        		<input type="button" value="<?php echo BTN_SEARCH; ?>" onclick="return search();" class="button">
        	</span>
        	
        </p>
        </fieldset>
  
      </div>
      
      <div class="clear"></div>
    </div>
		
  
  </div>
  <div class="clear"></div>


  
  <div id="ajaxLoading" style="display:none"><img class="ajaxLoadingImg" src="theme/<?php echo CONFIG_THEME_NAME; ?>/images/ajaxLoading.gif" /></div>
  <div id="winUpload" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
		  	<form id="formUpload" name="formUpload" method="post" enctype="multipart/form-data" action="">
		  	<table class="tableForm" cellpadding="0" cellspacing="0">
		  		<thead>
		  			<tr>
		  				<th colspan="2"><?php echo FILE_FORM_TITLE; ?><a class="action" href="#" title="<?php echo FILE_LBL_MORE;  ?>" onclick="return addMoreFile();"><span class="addMore">&nbsp;</span></a></th>
		  			</tr>
		  		</thead>
		  		<tbody id="fileUploadBody">
		  			<tr style="display:none">
		  				<th><label><?php echo FILE_LABEL_SELECT; ?></label></th>
		  				<td><input type="file" class="input" name="file"  /> <input type="button" class="button" value="<?php echo FILE_LBL_UPLOAD; ?>" /> <a href="#" class="action" title="Cancel" style="display:none" ><span class="cancel">&nbsp;</span></a>  <span class="uploadProcessing" style="display:none">&nbsp;<span></td>
		  			</tr>		
		  		</tbody>
		  		<tfoot>
		  			<tr>
		  				<th>&nbsp;</th>
		  				<td></td>
		  			</tr>
		  		</tfoot>
		  	</table>
		  	</form>  		
  		</div>

  	</div>
  </div> 
  <div id="winNewFolder" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
	    	<form id="formNewFolder" name="formNewFolder" method="post" action="">
	  	<input type="hidden" name="currentFolderPath" value="" id="currentNewfolderPath" />
	  	<table class="tableForm" cellpadding="0" cellspacing="0">
	  		<thead>
	  			<tr>
	  				<th colspan="2"><?php echo FOLDER_FORM_TITLE; ?></th>
	  			</tr>
	  		</thead>
	  		<tbody>
	  			<tr>
	  				<th><label><?php echo FOLDER_LBL_TITLE; ?></label></th>
	  				<td><input type="text" name="new_folder" id="new_folder" value="" class="input"></td>
	  			</tr>    		
	
				
	  		</tbody>
	  		<tfoot>
	  			<tr>
	  				<th>&nbsp;</th>
	  				<td><input type="button" value="<?php echo FOLDER_LBL_CREATE; ?>" class="button" onclick="return doCreateFolder();"  /></td>
	  			</tr>
	  		</tfoot>
	  	</table>	
	  	</form>	
  		</div>

  	
  	
  	</div>
  </div>   
  <div id="winPlay" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="closeWinPlay();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
  			<div id="playGround"></div>
  		</div>
  	</div>
  </div>
  <div id="winRename" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
		  	<form id="formRename" name="formRename" method="post" action="">
		  	<input type="hidden" name="original_path" id="original_path" />
		  	<input type="hidden" name="num" id="renameNum" value="" />
		  	<table class="tableForm" cellpadding="0" cellspacing="0">
		  		<thead>
		  			<tr>
		  				<th colspan="2"><?php echo RENAME_FORM_TITLE; ?></th>
		  			</tr>
		  		</thead>
		  		<tbody>
		  			<tr>
		  				<th><label><?php echo RENAME_NEW_NAME; ?></label></th>
		  				<td><input type="name" id="renameName" class="input" name="name" style="width:250px" /> 
		          </td>
		  			</tr>
		  		</tbody>
		  		<tfoot>
		  			<tr>
		  				<th>&nbsp;</th>
		  				<td><input type="button" value="<?php echo RENAME_LBL_RENAME; ?>" class="button" onclick="return doRename();"  /></td>
		  			</tr>
		  		</tfoot>
		  	</table>
		  	</form>
  		</div>  	

  	</div>
  	
  </div>        
  <div id="winInfo" style="display:none">
  	<div class="jqmContainer">
  		<div class="jqmHeader">
  			<a href="#" onclick="tb_remove();"><?php echo LBL_ACTION_CLOSE; ?></a>
  		</div>
  		<div class="jqmBody">
   	<table class="tableInfo" cellpadding="0" cellspacing="0">
  		<tbody>
  			<tr>
	  			<th nowrap>
	  				<label>Author:</label>
	  			</th>
	  			<td>
	  				<a href="&#109;a&#105;l&#116;&#111;:&#99;&#97;&#105;&#108;&#111;&#110;&#103;&#113;&#117;&#110;&#64;&#121;&#97;&#104;&#111;&#111;&#46;&#99;&#111;&#109;&#46;&#99;&#110;">Logan Cai</a>
	  			</td>
  			</tr>
  			<tr>
  				<th nowrap>
  					<label>Template Designer:</label>
  				</th>
  				<td>
  					<a href="&#109;a&#105;l&#116;&#111;:&#71;&#97;&#98;&#114;&#105;&#101;&#108;&#64;&#52;&#118;&#46;&#99;&#111;&#109;&#46;&#98;&#114;">Gabriel</a>
  				</td>
  			</tr>
  			<tr>
  				<th  nowrap>
  					<label>Official Website:</label>
  				</th>
  				<td>
  					<a href="http://www.phpletter.com">http://www.phpletter.com</a>
  				</td>
  			</tr>
  			<tr>
  				<th  nowrap>
  					<label>Support Forum:</label>
  				</th>
  				<td>
  					<a href="http://www.phpletter.com/forum/">http://www.phpletter.com/forum/</a>
  				</td>
  			</tr>
  			<tr>
  				<th nowrap>
  					<label>&copy;Copyright:</label>
  				</th>
  				<td>
  					All copyright declarations in the source must remain unchange. Please contact us if you need to make changes to it, in order to avoid any Legal Issues.  
  				</td>
  			</tr>
  		</tbody>
  	</table>
  		</div>  	


  	</div>
  </div>
  <div id="contextMenu" style="display:none">
  	<ul>
  		<li><a href="#" class="contentMenuItem" id="menuSelect"><?php echo MENU_SELECT; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuPreview"><?php echo MENU_PREVIEW; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuDownload"><?php echo MENU_DOWNLOAD; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuRename"><?php echo MENU_RENAME; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuEdit"><?php echo MENU_EDIT; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuCut"><?php echo MENU_CUT; ?></a></li>
  		<li><a href="#" class="contentMenuItem" id="menuCopy"><?php echo MENU_COPY; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuPaste"><?php echo MENU_PASTE; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuDelete"><?php echo MENU_DELETE; ?></a></li>
  		<li><a href="#" class="contentMenuItem"  id="menuPlay"><?php echo MENU_PLAY; ?></a></li>
  	</ul>
  </div>
</body>
</html>