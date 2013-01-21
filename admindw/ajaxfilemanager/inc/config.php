<?php
	/**
	 * sysem  config setting
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @version 1.0
	 * @since 22/April/2007
	 *
	 */
	
	//FILESYSTEM CONFIG	<br>
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "class.auth.php");	
	define('CONFIG_QUERY_STRING_ENABLE', true); //Enable passed query string to setting the system configuration
	if(!isset($_SESSION))
	{
		session_start();
	}
	if(!headers_sent())
	{
		header('Content-Type: text/html; charset=utf-8');
	}
	
	/**
	 * secure file name which retrieve from query string
	 *
	 * @param string $input
	 * @return string
	 */
	function secureFileName($input)
	{
		return preg_replace('/[^a-zA-Z0-9\-_]/', '', $input);
	}	
	//Directories Declarations	
	
	define('DIR_AJAX_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR) ; // the path to ajax file manager
	define('DIR_AJAX_INC', DIR_AJAX_ROOT . "inc" . DIRECTORY_SEPARATOR);
	define('DIR_AJAX_CLASSES', DIR_AJAX_ROOT .  "classes" . DIRECTORY_SEPARATOR);
	define("DIR_AJAX_LANGS", DIR_AJAX_ROOT . "langs" . DIRECTORY_SEPARATOR);
	define('DIR_AJAX_JS', DIR_AJAX_ROOT . 'jscripts' . DIRECTORY_SEPARATOR);
	define('DIR_AJAX_EDIT_AREA', DIR_AJAX_JS . 'edit_area' . DIRECTORY_SEPARATOR);
	define('DIR_LANG', DIR_AJAX_ROOT . 'langs' . DIRECTORY_SEPARATOR);

	
	//Class Declarations
	define('CLASS_FILE', DIR_AJAX_INC .'class.file.php');
	define("CLASS_UPLOAD", DIR_AJAX_INC .  'class.upload.php');
	define('CLASS_MANAGER', DIR_AJAX_INC . 'class.manager.php');
	define('CLASS_IMAGE', DIR_AJAX_INC . "class.image.php");
	define('CLASS_HISTORY', DIR_AJAX_INC . "class.history.php");
	define('CLASS_SESSION_ACTION', DIR_AJAX_INC . "class.sessionaction.php");
	define('CLASS_PAGINATION', DIR_AJAX_INC . 'class.pagination.php');
	define('CLASS_SEARCH', DIR_AJAX_INC . "class.search.php");
	//SCRIPT FILES declarations
	define('SPT_FUNCTION_BASE', DIR_AJAX_INC . 'function.base.php');	
	//include different config base file according to query string "config"
	$configBaseFileName = 'config.base.php';
	
	if(CONFIG_QUERY_STRING_ENABLE && !empty($_GET['config']) && file_exists(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'config.' . secureFileName($_GET['config']) . ".php")
	{
		$configBaseFileName = 'config.' . secureFileName($_GET['config']) . ".php";
	}
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . $configBaseFileName);

	
	require_once(DIR_AJAX_LANGS . CONFIG_LANG_DEFAULT . ".php");
	require_once(DIR_AJAX_INC . "function.base.php");	
	
	require_once(dirname(__FILE__) .  DIRECTORY_SEPARATOR . "class.session.php");
	$session = new Session();
	$auth = new Auth();
	
	if(CONFIG_ACCESS_CONTROL_MODE == 1)
	{//access control enabled
		if(!$auth->isLoggedIn() && strtolower(basename($_SERVER['PHP_SELF']) != strtolower(basename(CONFIG_LOGIN_PAGE))))
		{//
			header('Location: ' . appendQueryString(CONFIG_LOGIN_PAGE, makeQueryString()));
			exit;
		}
	}
	addNoCacheHeaders();
	//URL Declartions
	define('CONFIG_URL_IMAGE_PREVIEW', 'ajax_image_preview.php');
	define('CONFIG_URL_CREATE_FOLDER', 'ajax_create_folder.php');
	define('CONFIG_URL_DELETE', 'ajax_delete_file.php');
	define('CONFIG_URL_HOME', 'ajaxfilemanager.php');
	define("CONFIG_URL_UPLOAD", 'ajax_file_upload.php');
	define('CONFIG_URL_PREVIEW', 'ajax_preview.php');
	define('CONFIG_URL_SAVE_NAME', 'ajax_save_name.php');
	define('CONFIG_URL_IMAGE_EDITOR', 'ajax_image_editor.php');
	define('CONFIG_URL_IMAGE_SAVE', 'ajax_image_save.php');
	define('CONFIG_URL_IMAGE_RESET', 'ajax_editor_reset.php');
	define('CONFIG_URL_IMAGE_UNDO', 'ajax_image_undo.php');
	define('CONFIG_URL_CUT', 'ajax_file_cut.php');
	define('CONFIG_URL_COPY', 'ajax_file_copy.php');
	define('CONFIG_URL_LOAD_FOLDERS', '_ajax_load_folders.php');
	
	define('CONFIG_URL_DOWNLOAD', 'ajax_download.php');
	define('CONFIG_URL_TEXT_EDITOR', 'ajax_text_editor.php');
	define('CONFIG_URL_GET_FOLDER_LIST', 'ajax_get_folder_listing.php');
	define('CONFIG_URL_SAVE_TEXT', 'ajax_save_text.php');
	define('CONFIG_URL_LIST_LISTING', 'ajax_get_file_listing.php');
	define('CONFIG_URL_IMG_THUMBNAIL', 'ajax_image_thumbnail.php');
	define('CONFIG_URL_FILEnIMAGE_MANAGER', 'ajaxfilemanager.php');
	define('CONFIG_URL_FILE_PASTE', 'ajax_file_paste.php');
	

?>