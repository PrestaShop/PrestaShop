<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
		/**
	 * Ajax image editor platform
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_EDITABLE)
	{
		die(SYS_DISABLED);
	}	
		//$session->gc();
		$_GET['path'] = empty($_GET['path'])?CONFIG_SYS_ROOT_PATH . "ajax_image_editor_demo.jpg":$_GET['path'];
		if(!empty($_GET['path']) && file_exists($_GET['path']) && is_file($_GET['path']) && isUnderRoot($_GET['path']))
		{
				$path = $_GET['path'];
		}else 
		{
			die(TXT_FILE_NOT_FOUND);
		}
		if(file_exists(DIR_AJAX_EDIT_AREA . "reg_syntax" . DIRECTORY_SEPARATOR . getFileExt($path) . ".js"))
		{
			$syntax = getFileExt($path);			
		}else 
		{
			switch (getFileExt($path))
			{
				case 'htm':
					$syntax = 'html';
					break;
				default:
					$syntax = 'basic';
			}
		}
		if(array_search(getFileExt($path), getValidTextEditorExts())=== false)
		{
			die(TXT_DISALLOWED_EXT);	
		}
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Logan Cai" />
<meta name="website" content="http://www.phpletter.com" />
<script type="text/javascript" src="jscripts/ajaxtexteditor_c.js"></script>
<!--<script type="text/javascript" src="jscripts/jquery.js"></script>
<script type="text/javascript" src="jscripts/form.js"></script>
<script type="text/javascript" src="jscripts/select.js"></script>
<script type="text/javascript" src="jscripts/jqModal.js"></script>
<script type="text/javascript" src="jscripts/ajaxtexteditor.js"></script>
-->
<script type="text/javascript" src="jscripts/edit_area/edit_area_full.js"></script>
<script type="text/javascript">
				var warningExtNotSelected = '<?php echo TXT_EXT_NOT_SELECTED; ?>';
				var urlGetFolderList = '<?php echo appendQueryString(CONFIG_URL_GET_FOLDER_LIST, makeQueryString(array('path'))); ?>';
				var warningInvalidName = '<?php echo TXT_SAVE_AS_ERR_NAME_INVALID; ?>';
				var waringFolderNotSelected = '<?php echo TXT_DEST_FOLDER_NOT_SELECTED; ?>';
				var currentFolder = '<?php echo dirname($path); ?>';
				var currentName = '<?php echo basename($path); ?>';

		jQuery(document).ready(
		function()
		{
				editAreaLoader.init({				
				id: "content"	// id of the textarea to transform		
				,start_highlight: false	// if start with highlight
				,allow_resize: "both"
				,gecko_spellcheck:true
				,allow_toggle: true
				,toolbar:"search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, highlight, reset_highlight, |, save, save_as"
				,save_callback:"save"
				,save_as_callback:"save_as"
				,language: "<?php echo (file_exists(DIR_AJAX_EDIT_AREA . 'langs' . DIRECTORY_SEPARATOR .CONFIG_LANG_INDEX . ".js")?CONFIG_LANG_INDEX:'en'); ?>"
				,syntax: "<?php echo $syntax; ?>"	
			});				
				jQuery('#windowSaveAs').jqm();		
				jQuery('#windowProcessing').jqm({modal:true});				
		}
	);		

		

			
</script>

<link href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/ajaxtexteditor.css" type="text/css" rel="stylesheet" />
<link href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/jqModal.css" type="text/css" rel="stylesheet" />
<title>Ajax Text Editor</title>
</head>
<body>

<div id="pageBody">
	<textarea name="content" id="content" style="height:500px; width: 97%;"><?php echo getFileContent($path); ?></textarea>
</div>
<div id="windowProcessing" class="jqmWindow" style="display:none">
	<form name="frmProcessing" id="frmProcessing" method="post" action="<?php echo appendQueryString(CONFIG_URL_SAVE_TEXT, makeQueryString(array('path')));?>">
		<input type="hidden" name="folder" id="folder" value="<?php echo dirname($path); ?>" />
		<input type="hidden" name="name" id="name" value="<?php echo basename($path); ?>" />	
		<input type="hidden" name="save_as_request" id="save_as_request" value="0" />
		<div style="display:none"><textarea name="text" id="text"></textarea></div> 
	</form> 
	<a href="#" class="jqmClose" id="windowSaveClose"><?php echo IMG_BTN_CANCEL; ?></a>
	<p><img src="theme/<?php echo CONFIG_THEME_NAME; ?>/images/loading.gif" /></p>
</div>
<div id="windowSaveAs" class="jqmWindow" style="display:none">
    	<a href="#" class="jqmClose" id="windowSaveClose"><?php echo IMG_BTN_CANCEL; ?></a>
      <form id="formSaveAs" name="formSaveAs" action="" method="post">
    	<table class="tableForm" cellpadding="0" cellspacing="0">
      	<thead>
        	<tr>
          	<th colspan="2"><?php echo IMG_LBL_SAVE_AS; ?></th>
          </tr>
        </thead>
        <tbody>
        	<tr>
          	<th>
            	<label><?php echo IMG_LBL_NEW_NAME; ?></label>
            </th>
            <td>
            	<input type="text" id="new_name" class="input" name="new_name" value="" />
              &nbsp;.&nbsp;<select id="ext" name="ext">
              <?php
								foreach(getValidTextEditorExts() as $v)
								{
									?>
									<option value="<?php echo $v; ?>" <?php echo (strtolower($v) == strtolower(getFileExt($path))?'selected':''); ?>><?php echo $v; ?></option>
									<?php
								}
							?>
              </select>
            </td>
          </tr>
          <tr>
          	<th>
            	<label><?php echo IMG_LBL_SAVE_TO; ?></label>
            </th>
            <td>
            	<select class="input" name="save_to" id="save_to">
              	
              </select>
            </td>
          </tr>
          <tr>
          	<th>&nbsp;
            </th>
            <td>
            <span class="comments">*</span>
            <?php echo IMG_NEW_NAME_COMMENTS; ?>
            </td>
          </tr>
        </tbody>
        <tfoot>
        	<tr>
        	<th>&nbsp;</th>
          <td><input type="button" class="button" value="<?php echo IMG_BTN_SAVE_AS; ?>" onclick="return do_save_as();" /></td>
          </tr>
        </tfoot>
      </table>
      </form>
    </div>

</body></html>

