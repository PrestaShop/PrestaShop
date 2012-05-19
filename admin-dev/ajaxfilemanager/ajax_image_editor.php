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
			die(IMG_GEN_IMG_NOT_EXISTS);
		}
		require_once(CLASS_HISTORY);
		$history = new History($path, $session);
		if(CONFIG_SYS_DEMO_ENABLE)
		{
			$sessionImageInfo = $history->getLastestRestorable();
			$originalSessionImageInfo = $history->getOriginalImage();
			if(sizeof($originalSessionImageInfo))
			{
				$path = backslashToSlash($session->getSessionDir() . $originalSessionImageInfo['info']['name']);
			}
		}
		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "class.image.php");
		$image = new Image();
		
		$imageInfo = $image->getImageInfo($path);

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Logan Cai" />
<meta name="website" content="http://www.phpletter.com" />
<script type="text/javascript" src="jscripts/ajaximageeditor_c.js"></script>
<!--
<script type="text/javascript" src="jscripts/jquery.js"></script>
<script type="text/javascript" src="jscripts/form.js"></script>
<script type="text/javascript" src="jscripts/select.js"></script>
<script type="text/javascript" src="jscripts/jqModal.js"></script>
<script type="text/javascript" src="jscripts/rotate.js"></script>
<script type="text/javascript" src="jscripts/interface.js"></script>

-->
<script type="text/javascript" src="jscripts/ajaximageeditor.js"></script>






<script type="text/javascript">
	var imageHistory = false;
	var currentFolder =  '<?php echo removeTrailingSlash(backslashToSlash(dirname($path))); ?>';
	var warningLostChanges = '<?php echo IMG_WARNING_LOST_CHANAGES; ?>';
	var warningReset = '<?php echo IMG_WARNING_REST; ?>';
	var warningResetEmpty = '<?php echo IMG_WARNING_EMPTY_RESET; ?>';
	var warningEditorClose = '<?php echo IMG_WARING_WIN_CLOSE; ?>';
	var warningUndoImage = '<?php echo IMG_WARNING_UNDO; ?>';
	var warningFlipHorizotal = '<?php echo IMG_WARING_FLIP_H; ?>';
	var warningFlipVertical = '<?php echo IMG_WARING_FLIP_V; ?>';
	var numSessionHistory = <?php echo $history->getNumRestorable(); ?>;
	var noChangeMadeBeforeSave = '<?php echo IMG_WARNING_NO_CHANGE_BEFORE_SAVE; ?>';
	var warningInvalidNewName = '<?php echo IMG_SAVE_AS_ERR_NAME_INVALID; ?>';
	var wordCloseWindow = '<?php echo LBL_ACTION_CLOSE; ?>';
	var warningNoFolderSelected = '<?php echo IMG_SAVE_AS_NOT_FOLDER_SELECTED; ?>';
	var urlGetFolderList = '<?php echo appendQueryString(CONFIG_URL_GET_FOLDER_LIST, makeQueryString(array('path'))); ?>';
	$(document).ready(
		function()
		{
			$('#windowSaveAs').jqm();
			$('#image_mode').val('');
			$('#angle').val(0);
			$(getImageElement()).clone().appendTo("#hiddenImage");
			changeMode();
			initDisabledButtons(true);
		}
	);
	
</script>
<link href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/ajaximageeditor.css" type="text/css" rel="stylesheet" />
<link href="theme/<?php echo CONFIG_THEME_NAME; ?>/css/jqModal.css" type="text/css" rel="stylesheet" />
<title>Ajax Image Editor</title>
</head>
<body>
<?php
	//displayArray($_SESSION);
	 
?>
<div id="controls">
	<fieldset id="modes">
		<legend>Modes</legend>
		<form name="formAction" id="formAction" method="post" action="<?php echo appendQueryString(CONFIG_URL_IMAGE_UNDO, makeQueryString(array('path'))); ?>">
			<input type="hidden" name="file_path" id="file_path" value="<?php echo Tools::safeOutput($_GET['path']); ?>" />
			
			<p><label><?php echo IMG_MODE_RESIZE; ?></label> <input type="radio" name="mode" value="resize" class="input" checked="checked"  onclick="return changeMode();"/>
			<label><?php echo IMG_MODE_CROP; ?></label> <input type="radio" name="mode" value="crop" class="input" onclick="return changeMode();" />
			<label><?php echo IMG_MODE_ROTATE; ?></label> <input type="radio" name="mode" value="rotate" class="input" onclick="return changeMode();" />
			<label><?php echo IMG_MODE_FLIP; ?></label> <input type="radio" name="mode" value="flip" class="input" onclick="return changeMode();" />
			<label><?php echo IMG_CHECKBOX_CONSTRAINT; ?></label> <input type="checkbox" name="constraint" id="constraint" value="1" class="input" onclick="return toggleConstraint();" />
			<!--			<label>Watermark:</label> <input type="radio" name="mode" value="watermark" class="input" onclick="return false;" />-->
			
			<button id="actionRotateLeft" class="disabledButton" onclick="return leftRotate();" disabled><?php echo IMG_BTN_ROTATE_LEFT; ?></button>
			<button id="actionRotateRight" class="disabledButton" onclick="return rightRotate();" disabled><?php echo IMG_BTN_ROTATE_RIGHT; ?></button>
			<button id="actionFlipH" class="disabledButton" onclick="return flipHorizontal();" disabled><?php echo IMG_BTN_FLIP_H; ?></button>
			<button id="actionFlipV" class="disabledButton" onclick="return flipVertical();" disabled><?php echo IMG_BTN_FLIP_V; ?></button>			
			<button id="actionReset" class="button" onclick="return resetEditor();"><?php echo IMG_BTN_RESET; ?></button>
			<button id="actionUndo" class="button" onclick="return undoImage();"><?php echo IMG_BTN_UNDO; ?></button>
			<button id="actionSave" class="button" onclick="return saveImage();"><?php echo IMG_BTN_SAVE; ?></button>
      <button id="actionSaveAs" class="button" onclick="return saveAsImagePre();"><?php echo IMG_BTN_SAVE_AS; ?></button>
			<button id="actionClose" class="button" onclick="return editorClose();"><?php echo IMG_BTN_CLOSE; ?></button></p>
		</form>
	</fieldset>
	<fieldset id="imageInfo">
		<legend id="imageInfoLegend"><?php echo IMG_INFO; ?></legend>
		<form name="formImageInfo" action="<?php echo appendQueryString(CONFIG_URL_IMAGE_SAVE, makeQueryString(array('path'))); ?>" method="post" id="formImageInfo">
			<p><input type="hidden" name="mode" id="image_mode" value="" />
      <input type="hidden" name="new_name" id="hidden_new_name" value="" />
      <input type="hidden" name="save_to" id="hidden_save_to" value="" />
			<input type="hidden" name="path" id="path" value="<?php echo Tools::safeOutput($_GET['path']); ?>"  />
			<input type="hidden" name="flip_angle" id="flip_angle" value="" />
			<label><?php echo IMG_LBL_WIDTH; ?></label> <input type="text" name="width" id="width" value="" class="input imageInput"  />
			<label><?php echo IMG_LBL_HEIGHT; ?></label> <input type="text" name="height" id="height" value="" class="input imageInput" />
			<label><?php echo IMG_LBL_X; ?></label> <input type="text" name="x" id="x" value="" class="input imageInput"/>
			<label><?php echo IMG_LBL_Y; ?></label> <input type="text" name="y" id="y" value="" class="input imageInput"/>
<!--			<b>Percentage:</b> <input type="text" name="percentage" id="percentage" value="" class="input imageInput"/>-->
			<label><?php echo IMG_LBL_RATIO; ?></label> <input type="text" name="ratio" id="ratio" value="" class="input imageInput"/>
			<label><?php echo IMG_LBL_ANGLE; ?></label> <input type="text" name="angle" id="angle" value="" class="input imageInput" />
			
			</p>
		</form>
	</fieldset>
</div>
<div id="imageArea">
    <div id="imageContainer">
    	<img src="<?php echo $path; ?>" name="<?php echo basename($path); ?>" width="<?php echo $imageInfo['width']; ?>" height="<?php echo $imageInfo['height']; ?>" />
    </div>
    <div id="resizeMe">
    	<div id="resizeSE"></div>
    	<div id="resizeE"></div>
    	<div id="resizeNE"></div>
    	<div id="resizeN"></div>
    	<div id="resizeNW"></div>
    	<div id="resizeW"></div>
    	<div id="resizeSW"></div>
    	<div id="resizeS"></div>
		<img id="loading" style="display:none;" src="theme/<?php echo CONFIG_THEME_NAME; ?>/images/ajaxLoading.gif" />
    </div>
</div>
    <div id="hiddenImage">
    </div>
		<div id="windowSaveAs" class="jqmWindow" style="display:none">
    	<a href="#" class="jqmClose" id="windowSaveClose"><?php echo LBL_ACTION_CLOSE; ?></a>
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
            	<input type="text" id="new_name" class="input" name="new_name" value="" />&nbsp;.<?php echo getFileExt($path); ?>
            </td>
          </tr>
          <tr>
          	<th>
            	<label><?php echo IMG_LBL_SAVE_TO; ?></label>
            </th>
            <td>
            	<select class="input" name="save_to" id="save_to"></select>
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
          <td><input type="button" class="button" value="<?php echo IMG_BTN_SAVE_AS; ?>" onclick="return saveAsImage();" /></td>
          </tr>
        </tfoot>
      </table>
      </form>
    </div>
</body>
</html>
