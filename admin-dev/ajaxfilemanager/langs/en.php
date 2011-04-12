<?php
	/**
	 * language pack
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	define('DATE_TIME_FORMAT', 'd/M/Y H:i:s');
	//Common
	//Menu
	
	
	
	
	define('MENU_SELECT', 'Select');
	define('MENU_DOWNLOAD', 'Download');
	define('MENU_PREVIEW', 'Preview');
	define('MENU_RENAME', 'Rename');
	define('MENU_EDIT', 'Edit');
	define('MENU_CUT', 'Cut');
	define('MENU_COPY', 'Copy');
	define('MENU_DELETE', 'Delete');
	define('MENU_PLAY', 'Play');
	define('MENU_PASTE', 'Paste');
	
	//Label
		//Top Action
		define('LBL_ACTION_REFRESH', 'Refresh');
		define('LBL_ACTION_DELETE', 'Delete');
		define('LBL_ACTION_CUT', 'Cut');
		define('LBL_ACTION_COPY', 'Copy');
		define('LBL_ACTION_PASTE', 'Paste');
		define('LBL_ACTION_CLOSE', 'Close');
		define('LBL_ACTION_SELECT_ALL', 'Select All');
		//File Listing
	define('LBL_NAME', 'Name');
	define('LBL_SIZE', 'Size');
	define('LBL_MODIFIED', 'Modified At');
		//File Information
	define('LBL_FILE_INFO', 'File Information:');
	define('LBL_FILE_NAME', 'Name:');	
	define('LBL_FILE_CREATED', 'Created:');
	define('LBL_FILE_MODIFIED', 'Modified:');
	define('LBL_FILE_SIZE', 'File Size:');
	define('LBL_FILE_TYPE', 'File Type:');
	define('LBL_FILE_WRITABLE', 'Writable?');
	define('LBL_FILE_READABLE', 'Readable?');
		//Folder Information
	define('LBL_FOLDER_INFO', 'Folder Information');
	define('LBL_FOLDER_PATH', 'Folder:');
	define('LBL_CURRENT_FOLDER_PATH', 'Current Folder Path:');
	define('LBL_FOLDER_CREATED', 'Created:');
	define('LBL_FOLDER_MODIFIED', 'Modified:');
	define('LBL_FOLDER_SUDDIR', 'Subfolders:');
	define('LBL_FOLDER_FIELS', 'Files:');
	define('LBL_FOLDER_WRITABLE', 'Writable?');
	define('LBL_FOLDER_READABLE', 'Readable?');
	define('LBL_FOLDER_ROOT', 'Root Folder');
		//Preview
	define('LBL_PREVIEW', 'Preview');
	define('LBL_CLICK_PREVIEW', 'Click here to preview it.');
	//Buttons
	define('LBL_BTN_SELECT', 'Select');
	define('LBL_BTN_CANCEL', 'Cancel');
	define('LBL_BTN_UPLOAD', 'Upload');
	define('LBL_BTN_CREATE', 'Create');
	define('LBL_BTN_CLOSE', 'Close');
	define('LBL_BTN_NEW_FOLDER', 'New Folder');
	define('LBL_BTN_NEW_FILE', 'New File');
	define('LBL_BTN_EDIT_IMAGE', 'Edit');
	define('LBL_BTN_VIEW', 'Select View');
	define('LBL_BTN_VIEW_TEXT', 'Text');
	define('LBL_BTN_VIEW_DETAILS', 'Details');
	define('LBL_BTN_VIEW_THUMBNAIL', 'Thumbnails');
	define('LBL_BTN_VIEW_OPTIONS', 'View In:');
	//pagination
	define('PAGINATION_NEXT', 'Next');
	define('PAGINATION_PREVIOUS', 'Previous');
	define('PAGINATION_LAST', 'Last');
	define('PAGINATION_FIRST', 'First');
	define('PAGINATION_ITEMS_PER_PAGE', 'Display %s items per page');
	define('PAGINATION_GO_PARENT', 'Go Parent Folder');
	//System
	define('SYS_DISABLED', 'Permission denied: The system is disabled.');
	
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', 'No document(s) selected for cut.');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', 'No document(s) selected for copy.');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', 'No document(s) selected for paste.');
	define('WARNING_CUT_PASTE', 'Are you sure to move selected documents to current folder?');
	define('WARNING_COPY_PASTE', 'Are you sure to copy selected documents to current folder?');
	define('ERR_NOT_DEST_FOLDER_SPECIFIED', 'No destination folder specified.');
	define('ERR_DEST_FOLDER_NOT_FOUND', 'Destination folder not found.');
	define('ERR_DEST_FOLDER_NOT_ALLOWED', 'You are not allowed to move files to this folder');
	define('ERR_UNABLE_TO_MOVE_TO_SAME_DEST', 'Failed to move this file (%s): Original path is same as destination path.');
	define('ERR_UNABLE_TO_MOVE_NOT_FOUND', 'Failed to move this file (%s): Original file does not exist.');
	define('ERR_UNABLE_TO_MOVE_NOT_ALLOWED', 'Failed to move this file (%s): Original file access denied.');
 
	define('ERR_NOT_FILES_PASTED', 'No file(s) has been pasted.');

	//Search
	define('LBL_SEARCH', 'Search');
	define('LBL_SEARCH_NAME', 'Full/Partial File Name:');
	define('LBL_SEARCH_FOLDER', 'Look in:');
	define('LBL_SEARCH_QUICK', 'Quick Search');
	define('LBL_SEARCH_MTIME', 'File Modified Time(Range):');
	define('LBL_SEARCH_SIZE', 'File Size:');
	define('LBL_SEARCH_ADV_OPTIONS', 'Advanced Options');
	define('LBL_SEARCH_FILE_TYPES', 'File Types:');
	define('SEARCH_TYPE_EXE', 'Application');
	
	define('SEARCH_TYPE_IMG', 'Image');
	define('SEARCH_TYPE_ARCHIVE', 'Archive');
	define('SEARCH_TYPE_HTML', 'HTML');
	define('SEARCH_TYPE_VIDEO', 'Video');
	define('SEARCH_TYPE_MOVIE', 'Movie');
	define('SEARCH_TYPE_MUSIC', 'Music');
	define('SEARCH_TYPE_FLASH', 'Flash');
	define('SEARCH_TYPE_PPT', 'PowerPoint');
	define('SEARCH_TYPE_DOC', 'Document');
	define('SEARCH_TYPE_WORD', 'Word');
	define('SEARCH_TYPE_PDF', 'PDF');
	define('SEARCH_TYPE_EXCEL', 'Excel');
	define('SEARCH_TYPE_TEXT', 'Text');
	define('SEARCH_TYPE_UNKNOWN', 'Unknown');
	define('SEARCH_TYPE_XML', 'XML');
	define('SEARCH_ALL_FILE_TYPES', 'All File Types');
	define('LBL_SEARCH_RECURSIVELY', 'Search Recursively:');
	define('LBL_RECURSIVELY_YES', 'Yes');
	define('LBL_RECURSIVELY_NO', 'No');
	define('BTN_SEARCH', 'Search Now');
	//thickbox
	define('THICKBOX_NEXT', 'Next&gt;');
	define('THICKBOX_PREVIOUS', '&lt;Prev');
	define('THICKBOX_CLOSE', 'Close');
	//Calendar
	define('CALENDAR_CLOSE', 'Close');
	define('CALENDAR_CLEAR', 'Clear');
	define('CALENDAR_PREVIOUS', '&lt;Prev');
	define('CALENDAR_NEXT', 'Next&gt;');
	define('CALENDAR_CURRENT', 'Today');
	define('CALENDAR_MON', 'Mon');
	define('CALENDAR_TUE', 'Tue');
	define('CALENDAR_WED', 'Wed');
	define('CALENDAR_THU', 'Thu');
	define('CALENDAR_FRI', 'Fri');
	define('CALENDAR_SAT', 'Sat');
	define('CALENDAR_SUN', 'Sun');
	define('CALENDAR_JAN', 'Jan');
	define('CALENDAR_FEB', 'Feb');
	define('CALENDAR_MAR', 'Mar');
	define('CALENDAR_APR', 'Apr');
	define('CALENDAR_MAY', 'May');
	define('CALENDAR_JUN', 'Jun');
	define('CALENDAR_JUL', 'Jul');
	define('CALENDAR_AUG', 'Aug');
	define('CALENDAR_SEP', 'Sep');
	define('CALENDAR_OCT', 'Oct');
	define('CALENDAR_NOV', 'Nov');
	define('CALENDAR_DEC', 'Dec');
	//ERROR MESSAGES
		//deletion
	define('ERR_NOT_FILE_SELECTED', 'Please select a file.');
	define('ERR_NOT_DOC_SELECTED', 'No document(s) selected for deletion.');
	define('ERR_DELTED_FAILED', 'Unable to delete selected document(s).');
	define('ERR_FOLDER_PATH_NOT_ALLOWED', 'The folder path is not allowed.');
		//class manager
	define('ERR_FOLDER_NOT_FOUND', 'Unable to locate the specific folder: ');
		//rename
	define('ERR_RENAME_FORMAT', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	define('ERR_RENAME_EXISTS', 'Please give it a name which is unique under the folder.');
	define('ERR_RENAME_FILE_NOT_EXISTS', 'The file/folder does not exist.');
	define('ERR_RENAME_FAILED', 'Unable to rename it, please try again.');
	define('ERR_RENAME_EMPTY', 'Please give it a name.');
	define('ERR_NO_CHANGES_MADE', 'No changes has been made.');
	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', 'You are not permitted to change the file to such extension.');
		//folder creation
	define('ERR_FOLDER_FORMAT', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	define('ERR_FOLDER_EXISTS', 'Please give it a name which is unique under the folder.');
	define('ERR_FOLDER_CREATION_FAILED', 'Unable to create a folder, please try again.');
	define('ERR_FOLDER_NAME_EMPTY', 'Please give it  a name.');
	define('FOLDER_FORM_TITLE', 'New Folder Form');
	define('FOLDER_LBL_TITLE', 'Folder Title:');
	define('FOLDER_LBL_CREATE', 'Create Folder');
	//New File
	define('NEW_FILE_FORM_TITLE', 'New File Form');
	define('NEW_FILE_LBL_TITLE', 'File Name:');
	define('NEW_FILE_CREATE', 'Create File');
		//file upload
	define('ERR_FILE_NAME_FORMAT', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	define('ERR_FILE_NOT_UPLOADED', 'No file has been selected for uploading.');
	define('ERR_FILE_TYPE_NOT_ALLOWED', 'You are not allowed to upload such file type.');
	define('ERR_FILE_MOVE_FAILED', 'Failed to move the file.');
	define('ERR_FILE_NOT_AVAILABLE', 'The file is unavailable.');
	define('ERROR_FILE_TOO_BID', 'File too large. (max: %s)');
	define('FILE_FORM_TITLE', 'File Upload Form');
	define('FILE_LABEL_SELECT', 'Select File');
	define('FILE_LBL_MORE', 'Add File Uploader');
	define('FILE_CANCEL_UPLOAD', 'Cancel File Upload');
	define('FILE_LBL_UPLOAD', 'Upload');
	//file download
	define('ERR_DOWNLOAD_FILE_NOT_FOUND', 'No files selected for download.');
	//Rename
	define('RENAME_FORM_TITLE', 'Rename Form');
	define('RENAME_NEW_NAME', 'New Name');
	define('RENAME_LBL_RENAME', 'Rename');

	//Tips
	define('TIP_FOLDER_GO_DOWN', 'Single Click to get to this folder...');
	define('TIP_DOC_RENAME', 'Double Click to edit...');
	define('TIP_FOLDER_GO_UP', 'Single Click to get to the parent folder...');
	define('TIP_SELECT_ALL', 'Select All');
	define('TIP_UNSELECT_ALL', 'Unselect All');
	//WARNING
	define('WARNING_DELETE', 'Are you sure to delete selected document(s).');
	define('WARNING_IMAGE_EDIT', 'Please select an image for edit.');
	define('WARNING_NOT_FILE_EDIT', 'Please select a file for edit.');
	define('WARING_WINDOW_CLOSE', 'Are you sure to close the window?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', 'No preview available.');
	define('PREVIEW_OPEN_FAILED', 'Unable to open the file.');
	define('PREVIEW_IMAGE_LOAD_FAILED', 'Unable to load the image');

	//Login
	define('LOGIN_PAGE_TITLE', 'Ajax File Manager Login Form');
	define('LOGIN_FORM_TITLE', 'Login Form');
	define('LOGIN_USERNAME', 'Username:');
	define('LOGIN_PASSWORD', 'Password:');
	define('LOGIN_FAILED', 'Invalid username/password.');
	
	
	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', 'You have not made any changes to the images.');
		
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', 'Image does not exist');
		define('IMG_WARNING_LOST_CHANAGES', 'All unsaved changes made to the image will lost, are you sure you wish to continue?');
		define('IMG_WARNING_REST', 'All unsaved changes made to the image will be lost, are you sure to reset?');
		define('IMG_WARNING_EMPTY_RESET', 'No changes has been made to the image so far');
		define('IMG_WARING_WIN_CLOSE', 'Are you sure to close the window?');
		define('IMG_WARNING_UNDO', 'Are you sure to restore the image to previous state?');
		define('IMG_WARING_FLIP_H', 'Are you sure to flip the image horizotally?');
		define('IMG_WARING_FLIP_V', 'Are you sure to flip the image vertically');
		define('IMG_INFO', 'Image Information');
		
		//Mode
			define('IMG_MODE_RESIZE', 'Resize:');
			define('IMG_MODE_CROP', 'Crop:');
			define('IMG_MODE_ROTATE', 'Rotate:');
			define('IMG_MODE_FLIP', 'Flip:');		
		//Button
		
			define('IMG_BTN_ROTATE_LEFT', '90&deg;CCW');
			define('IMG_BTN_ROTATE_RIGHT', '90&deg;CW');
			define('IMG_BTN_FLIP_H', 'Flip Horizontal');
			define('IMG_BTN_FLIP_V', 'Flip Vertical');
			define('IMG_BTN_RESET', 'Reset');
			define('IMG_BTN_UNDO', 'Undo');
			define('IMG_BTN_SAVE', 'Save');
			define('IMG_BTN_CLOSE', 'Close');
			define('IMG_BTN_SAVE_AS', 'Save As');
			define('IMG_BTN_CANCEL', 'Cancel');
		//Checkbox
			define('IMG_CHECKBOX_CONSTRAINT', 'Constraint?');
		//Label
			define('IMG_LBL_WIDTH', 'Width:');
			define('IMG_LBL_HEIGHT', 'Height:');
			define('IMG_LBL_X', 'X:');
			define('IMG_LBL_Y', 'Y:');
			define('IMG_LBL_RATIO', 'Ratio:');
			define('IMG_LBL_ANGLE', 'Angle:');
			define('IMG_LBL_NEW_NAME', 'New Name:');
			define('IMG_LBL_SAVE_AS', 'Save As Form');
			define('IMG_LBL_SAVE_TO', 'Save To:');
			define('IMG_LBL_ROOT_FOLDER', 'Root Folder');
		//Editor
		//Save as 
		define('IMG_NEW_NAME_COMMENTS', 'Please do not contain the image extension.');
		define('IMG_SAVE_AS_ERR_NAME_INVALID', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
		define('IMG_SAVE_AS_NOT_FOLDER_SELECTED', 'No distination folder selected.');	
		define('IMG_SAVE_AS_FOLDER_NOT_FOUND', 'The destination folder doest not exist.');
		define('IMG_SAVE_AS_NEW_IMAGE_EXISTS', 'There exists an image with same name.');

		//Save
		define('IMG_SAVE_EMPTY_PATH', 'Empty image path.');
		define('IMG_SAVE_NOT_EXISTS', 'Image does not exist.');
		define('IMG_SAVE_PATH_DISALLOWED', 'You are not allowed to access this file.');
		define('IMG_SAVE_UNKNOWN_MODE', 'Unexpected Image Operation Mode');
		define('IMG_SAVE_RESIZE_FAILED', 'Failed to resize the image.');
		define('IMG_SAVE_CROP_FAILED', 'Failed to crop the image.');
		define('IMG_SAVE_FAILED', 'Failed to save the image.');
		define('IMG_SAVE_BACKUP_FAILED', 'Unable to backup the original image.');
		define('IMG_SAVE_ROTATE_FAILED', 'Unable to rotate the image.');
		define('IMG_SAVE_FLIP_FAILED', 'Unable to flip the image.');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', 'Unable to open image from session.');
		define('IMG_SAVE_IMG_OPEN_FAILED', 'Unable to open image');
		
		
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', 'No history avaiable for undo.');
		define('IMG_UNDO_COPY_FAILED', 'Unable to restore the image.');
		define('IMG_UNDO_DEL_FAILED', 'Unable to delete the session image');
	
	//88888888888   Above for Image Editor   888888888888888888888
	
	//88888888888   Session   888888888888888888888
		define('SESSION_PERSONAL_DIR_NOT_FOUND', 'Unable to find the dedicated folder which should have been created under session folder');
		define('SESSION_COUNTER_FILE_CREATE_FAILED', 'Unable to open the session counter file.');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', 'Unable to write the session counter file.');
	//88888888888   Session   888888888888888888888
	
	//88888888888   Below for Text Editor   888888888888888888888
		define('TXT_FILE_NOT_FOUND', 'File is not found.');
		define('TXT_EXT_NOT_SELECTED', 'Please select file extension');
		define('TXT_DEST_FOLDER_NOT_SELECTED', 'Please select destination folder');
		define('TXT_UNKNOWN_REQUEST', 'Unknown Request.');
		define('TXT_DISALLOWED_EXT', 'You are allowed to edit/add such file type.');
		define('TXT_FILE_EXIST', 'Such file already exits.');
		define('TXT_FILE_NOT_EXIST', 'No such found.');
		define('TXT_CREATE_FAILED', 'Failed to create a new file.');
		define('TXT_CONTENT_WRITE_FAILED', 'Failed to write content to the file.');
		define('TXT_FILE_OPEN_FAILED', 'Failed to open the file.');
		define('TXT_CONTENT_UPDATE_FAILED', 'Failed to update content to the file.');
		define('TXT_SAVE_AS_ERR_NAME_INVALID', 'Please give it a name which only contain letters, digits, space, hyphen and underscore.');
	//88888888888   Above for Text Editor   888888888888888888888
	
	
?>