<?php
	/**
	 * language pack
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	define('DATE_TIME_FORMAT', 'Y/m/d H:i:s');
	//Common
	//Menu
	
	
	
	
	define('MENU_SELECT', '选取');
	define('MENU_DOWNLOAD', '下载');
	define('MENU_PREVIEW', '预览');
	define('MENU_RENAME', '更名');
	define('MENU_EDIT', '编辑');
	define('MENU_CUT', '剪切');
	define('MENU_COPY', '复制');
	define('MENU_DELETE', '删除');
	define('MENU_PLAY', '播放');
	define('MENU_PASTE', '粘贴');
	
	//Label
		//Top Action
		define('LBL_ACTION_REFRESH', '刷新');
		define('LBL_ACTION_DELETE', '删除');
		define('LBL_ACTION_CUT', '剪切');
		define('LBL_ACTION_COPY', '复制');
		define('LBL_ACTION_PASTE', '粘贴');
		define('LBL_ACTION_CLOSE', '关闭');
		define('LBL_ACTION_SELECT_ALL', '选取所有');
		//File Listing
	define('LBL_NAME', '文件名');
	define('LBL_SIZE', '大小');
	define('LBL_MODIFIED', '更改于');
		//File Information
	define('LBL_FILE_INFO', '文件信息:');
	define('LBL_FILE_NAME', '文件名:');	
	define('LBL_FILE_CREATED', '创建于:');
	define('LBL_FILE_MODIFIED', '更改于:');
	define('LBL_FILE_SIZE', '大小:');
	define('LBL_FILE_TYPE', '类型:');
	define('LBL_FILE_WRITABLE', '可写?');
	define('LBL_FILE_READABLE', '可读?');
		//Folder Information
	define('LBL_FOLDER_INFO', '文件夹信息');
	define('LBL_FOLDER_PATH', '文件夹:');
	define('LBL_CURRENT_FOLDER_PATH', '当前文件夹路径:');
	define('LBL_FOLDER_CREATED', '创建于:');
	define('LBL_FOLDER_MODIFIED', '更改于:');
	define('LBL_FOLDER_SUDDIR', '子目录:');
	define('LBL_FOLDER_FIELS', '子文件:');
	define('LBL_FOLDER_WRITABLE', '可写?');
	define('LBL_FOLDER_READABLE', '可读?');
	define('LBL_FOLDER_ROOT', '根文件夹');
		//Preview
	define('LBL_PREVIEW', '预览');
	define('LBL_CLICK_PREVIEW', '单击预览.');
	//Buttons
	define('LBL_BTN_SELECT', '选取');
	define('LBL_BTN_CANCEL', '取消');
	define('LBL_BTN_UPLOAD', '上传');
	define('LBL_BTN_CREATE', '创建');
	define('LBL_BTN_CLOSE', '关闭');
	define('LBL_BTN_NEW_FOLDER', '新文件夹');
	define('LBL_BTN_NEW_FILE', '上传文件');
	define('LBL_BTN_EDIT_IMAGE', '编辑');
	define('LBL_BTN_VIEW', '选择浏览模式');
	define('LBL_BTN_VIEW_TEXT', '文件');
	define('LBL_BTN_VIEW_DETAILS', '列表');
	define('LBL_BTN_VIEW_THUMBNAIL', '缩略图');
	define('LBL_BTN_VIEW_OPTIONS', '浏览模式:');
	//pagination
	define('PAGINATION_NEXT', '后页');
	define('PAGINATION_PREVIOUS', '前页');
	define('PAGINATION_LAST', '末页');
	define('PAGINATION_FIRST', '首页');
	define('PAGINATION_ITEMS_PER_PAGE', '每页显示%s文档');
	define('PAGINATION_GO_PARENT', '返回上一层');
	//System
	define('SYS_DISABLED', '系统已关闭');
	
	//Cut
	define('ERR_NOT_DOC_SELECTED_FOR_CUT', '没有文档被选取.');
	//Copy
	define('ERR_NOT_DOC_SELECTED_FOR_COPY', '没有文档被选取.');
	//Paste
	define('ERR_NOT_DOC_SELECTED_FOR_PASTE', '没有文档被选取.');
	define('WARNING_CUT_PASTE', '确定要移动选择的文档到当前的文件夹?');
	define('WARNING_COPY_PASTE', '确定要复制选择的文档到当前的文件夹?');
	define('ERR_NOT_DEST_FOLDER_SPECIFIED', '目标文件夹未定义');
	define('ERR_DEST_FOLDER_NOT_FOUND', '无法找到目标文件夹');
	define('ERR_DEST_FOLDER_NOT_ALLOWED', '不允许移动文档到些文件夹');
	define('ERR_UNABLE_TO_MOVE_TO_SAME_DEST', '无法移动%s文件夹: 目标文件夹与被移动的相同');
	define('ERR_UNABLE_TO_MOVE_NOT_FOUND', '无法移动%s文档: 被移动的文档已不存在');
	define('ERR_UNABLE_TO_MOVE_NOT_ALLOWED', '无法移动%s文档: 被移动的文档无法访问');
 
	define('ERR_NOT_FILES_PASTED', '没有文档被粘贴');

	//Search
	define('LBL_SEARCH', '搜索');
	define('LBL_SEARCH_NAME', '完全/部分文件名');
	define('LBL_SEARCH_FOLDER', '在文件夹下搜索:');
	define('LBL_SEARCH_QUICK', '快速搜索');
	define('LBL_SEARCH_MTIME', '文件更改时间:');
	define('LBL_SEARCH_SIZE', '文件大小:');
	define('LBL_SEARCH_ADV_OPTIONS', '高级搜索');
	define('LBL_SEARCH_FILE_TYPES', '文件类型:');
	define('SEARCH_TYPE_EXE', '可执行文件');
	
	define('SEARCH_TYPE_IMG', '图像');
	define('SEARCH_TYPE_ARCHIVE', '压缩文档');
	define('SEARCH_TYPE_HTML', 'HTML');
	define('SEARCH_TYPE_VIDEO', '影像');
	define('SEARCH_TYPE_MOVIE', '电影');
	define('SEARCH_TYPE_MUSIC', '音像');
	define('SEARCH_TYPE_FLASH', 'Flash');
	define('SEARCH_TYPE_PPT', '演示文稿');
	define('SEARCH_TYPE_DOC', '文档');
	define('SEARCH_TYPE_WORD', 'Word文档');
	define('SEARCH_TYPE_PDF', 'PDF');
	define('SEARCH_TYPE_EXCEL', 'Excel');
	define('SEARCH_TYPE_TEXT', '文本文件');
	define('SEARCH_TYPE_UNKNOWN', '示知');
	define('SEARCH_TYPE_XML', 'XML');
	define('SEARCH_ALL_FILE_TYPES', '所有的文件类型');
	define('LBL_SEARCH_RECURSIVELY', '搜索子文件夹:');
	define('LBL_RECURSIVELY_YES', '是');
	define('LBL_RECURSIVELY_NO', '否');
	define('BTN_SEARCH', '搜索');
	//thickbox
	define('THICKBOX_NEXT', '下一张&gt;');
	define('THICKBOX_PREVIOUS', '&lt;前一张');
	define('THICKBOX_CLOSE', '关闭');
	//Calendar
	define('CALENDAR_CLOSE', '关闭');
	define('CALENDAR_CLEAR', '清除');
	define('CALENDAR_PREVIOUS', '&lt;向前');
	define('CALENDAR_NEXT', '向后&gt;');
	define('CALENDAR_CURRENT', '今天');
	define('CALENDAR_MON', '星期一');
	define('CALENDAR_TUE', '星期二');
	define('CALENDAR_WED', '星期三');
	define('CALENDAR_THU', '星期四');
	define('CALENDAR_FRI', '星期五');
	define('CALENDAR_SAT', '星期六');
	define('CALENDAR_SUN', '星期日');
	define('CALENDAR_JAN', '一月');
	define('CALENDAR_FEB', '二月');
	define('CALENDAR_MAR', '三月');
	define('CALENDAR_APR', '四月');
	define('CALENDAR_MAY', '五月');
	define('CALENDAR_JUN', '六月');
	define('CALENDAR_JUL', '七月');
	define('CALENDAR_AUG', '八月');
	define('CALENDAR_SEP', '九月');
	define('CALENDAR_OCT', '十月');
	define('CALENDAR_NOV', '十一月');
	define('CALENDAR_DEC', '十二月');
	//ERROR MESSAGES
		//deletion
	define('ERR_NOT_FILE_SELECTED', '请选择文档');
	define('ERR_NOT_DOC_SELECTED', '请选择要删除的文档');
	define('ERR_DELTED_FAILED', '无法删除所选择的文档.');
	define('ERR_FOLDER_PATH_NOT_ALLOWED', '不允许删除文件夹内的文件.');
		//class manager
	define('ERR_FOLDER_NOT_FOUND', '无法找到指定的文件夹: ');
		//rename
	define('ERR_RENAME_FORMAT', '文件名只能含有英文字母,数字, - 和 _');
	define('ERR_RENAME_EXISTS', '相同名称的文档已存在');
	define('ERR_RENAME_FILE_NOT_EXISTS', '所选取的原文件不存在.');
	define('ERR_RENAME_FAILED', '无法重命名,请重试.');
	define('ERR_RENAME_EMPTY', '请输入新文档名.');
	define('ERR_NO_CHANGES_MADE', '文件名没有变化.');
	define('ERR_RENAME_FILE_TYPE_NOT_PERMITED', '不允许更改文档到此类扩展名.');
		//folder creation
	define('ERR_FOLDER_FORMAT', '文件名只能含有英文字母,数字, - 和 _');
	define('ERR_FOLDER_EXISTS', '此文件夹已存在.');
	define('ERR_FOLDER_CREATION_FAILED', '无法创建文件夹,请重试.');
	define('ERR_FOLDER_NAME_EMPTY', '请填写文件夹的名称.');
	define('FOLDER_FORM_TITLE', '新建文件夹窗口');
	define('FOLDER_LBL_TITLE', '名称:');
	define('FOLDER_LBL_CREATE', '新建文件夹');
	//New File
	define('NEW_FILE_FORM_TITLE', '新建文档窗口');
	define('NEW_FILE_LBL_TITLE', '名称:');
	define('NEW_FILE_CREATE', '新建文档');
		//file upload
	define('ERR_FILE_NAME_FORMAT', '文件名只能含有英文字母,数字, - 和 _');
	define('ERR_FILE_NOT_UPLOADED', '没有文件被选取上传.');
	define('ERR_FILE_TYPE_NOT_ALLOWED', '此类文件不允许上传.');
	define('ERR_FILE_MOVE_FAILED', '无法移动上传的文件.');
	define('ERR_FILE_NOT_AVAILABLE', '文件不存在.');
	define('ERROR_FILE_TOO_BID', '上传的文件太大(最大允许: %s)');
	define('FILE_FORM_TITLE', '文件上传窗口');
	define('FILE_LABEL_SELECT', '选取文件');
	define('FILE_LBL_MORE', '添加上传栏');
	define('FILE_CANCEL_UPLOAD', '取消上传');
	define('FILE_LBL_UPLOAD', '上传');
	//file download
	define('ERR_DOWNLOAD_FILE_NOT_FOUND', '没有文档被选取下载');
	//Rename
	define('RENAME_FORM_TITLE', '重命名窗口');
	define('RENAME_NEW_NAME', '新名称');
	define('RENAME_LBL_RENAME', '重命名');

	//Tips
	define('TIP_FOLDER_GO_DOWN', '单击进入文件夹...');
	define('TIP_DOC_RENAME', '双击重命名...');
	define('TIP_FOLDER_GO_UP', '单击返回上一层...');
	define('TIP_SELECT_ALL', '选取所有文档');
	define('TIP_UNSELECT_ALL', '取消所有选取');
	//WARNING
	define('WARNING_DELETE', '确定要删除所选取的文档.');
	define('WARNING_IMAGE_EDIT', '请选取要编辑的图像.');
	define('WARNING_NOT_FILE_EDIT', '请选取要编辑的文件.');
	define('WARING_WINDOW_CLOSE', '确定要关闭窗口?');
	//Preview
	define('PREVIEW_NOT_PREVIEW', '没有预览图.');
	define('PREVIEW_OPEN_FAILED', '无法打开文件.');
	define('PREVIEW_IMAGE_LOAD_FAILED', '无法加载图像');

	//Login
	define('LOGIN_PAGE_TITLE', '文件与图像管理器');
	define('LOGIN_FORM_TITLE', '登录窗口');
	define('LOGIN_USERNAME', '用户名:');
	define('LOGIN_PASSWORD', '密码:');
	define('LOGIN_FAILED', '无效的用户名/密码.');
	
	
	//88888888888   Below for Image Editor   888888888888888888888
		//Warning 
		define('IMG_WARNING_NO_CHANGE_BEFORE_SAVE', '没有任何对此图像的操作记录.');
		
		//General
		define('IMG_GEN_IMG_NOT_EXISTS', '图像不存在');
		define('IMG_WARNING_LOST_CHANAGES', '所有未保存的操作将丢失,确定要继续?');
		define('IMG_WARNING_REST', '所有操作将被取消,确定要重置?');
		define('IMG_WARNING_EMPTY_RESET', '还没有任何操作记录');
		define('IMG_WARING_WIN_CLOSE', '确定要关闭窗口?');
		define('IMG_WARNING_UNDO', '确定要恢复到上一步的操作?');
		define('IMG_WARING_FLIP_H', '确定要水平翻转?');
		define('IMG_WARING_FLIP_V', '确定要垂直翻转?');
		define('IMG_INFO', '图像信息');
		
		//Mode
			define('IMG_MODE_RESIZE', '收放:');
			define('IMG_MODE_CROP', '剪切:');
			define('IMG_MODE_ROTATE', '旋转:');
			define('IMG_MODE_FLIP', '翻转:');		
		//Button
		
			define('IMG_BTN_ROTATE_LEFT', '90&deg;逆转');
			define('IMG_BTN_ROTATE_RIGHT', '90&deg;顺转');
			define('IMG_BTN_FLIP_H', '垂直翻转');
			define('IMG_BTN_FLIP_V', '水平翻转');
			define('IMG_BTN_RESET', '重置');
			define('IMG_BTN_UNDO', '退一步');
			define('IMG_BTN_SAVE', '保存');
			define('IMG_BTN_CLOSE', '关闭');
			define('IMG_BTN_SAVE_AS', '另存为');
			define('IMG_BTN_CANCEL', '取消');
		//Checkbox
			define('IMG_CHECKBOX_CONSTRAINT', '保持比例?');
		//Label
			define('IMG_LBL_WIDTH', '宽:');
			define('IMG_LBL_HEIGHT', '高:');
			define('IMG_LBL_X', 'X轴:');
			define('IMG_LBL_Y', 'Y轴:');
			define('IMG_LBL_RATIO', '比例:');
			define('IMG_LBL_ANGLE', '角度:');
			define('IMG_LBL_NEW_NAME', '新名称:');
			define('IMG_LBL_SAVE_AS', '另存为窗口');
			define('IMG_LBL_SAVE_TO', '保存至:');
			define('IMG_LBL_ROOT_FOLDER', '根文件夹');
		//Editor
		//Save as 
		define('IMG_NEW_NAME_COMMENTS', '请别附加图像文档的扩展名');
		define('IMG_SAVE_AS_ERR_NAME_INVALID', '文件名只能含有英文字母,数字, - 和 _');
		define('IMG_SAVE_AS_NOT_FOLDER_SELECTED', '没有选取目标文件夹.');	
		define('IMG_SAVE_AS_FOLDER_NOT_FOUND', '目标文件夹不存在.');
		define('IMG_SAVE_AS_NEW_IMAGE_EXISTS', '相同的文件名已存在.');

		//Save
		define('IMG_SAVE_EMPTY_PATH', '图像路径是空的.');
		define('IMG_SAVE_NOT_EXISTS', '图像不存在.');
		define('IMG_SAVE_PATH_DISALLOWED', '无法访问选定的文档.');
		define('IMG_SAVE_UNKNOWN_MODE', '未知的图像操作模式');
		define('IMG_SAVE_RESIZE_FAILED', '缩放图像失败.');
		define('IMG_SAVE_CROP_FAILED', '剪切图像失败.');
		define('IMG_SAVE_FAILED', '无法保存图像.');
		define('IMG_SAVE_BACKUP_FAILED', '无法备份原图像');
		define('IMG_SAVE_ROTATE_FAILED', '无法旋转图像');
		define('IMG_SAVE_FLIP_FAILED', '无法翻转图像');
		define('IMG_SAVE_SESSION_IMG_OPEN_FAILED', '无法从进程文件夹打开图像文档');
		define('IMG_SAVE_IMG_OPEN_FAILED', '无法打开图像文档');
		
		
		//UNDO
		define('IMG_UNDO_NO_HISTORY_AVAIALBE', '没有记录可恢复');
		define('IMG_UNDO_COPY_FAILED', '无法恢复原来的图像文档');
		define('IMG_UNDO_DEL_FAILED', '无法删除进程文件夹中的图像文档');
	
	//88888888888   Above for Image Editor   888888888888888888888
	
	//88888888888   Session   888888888888888888888
		define('SESSION_PERSONAL_DIR_NOT_FOUND', '个人进程文件夹不存在');
		define('SESSION_COUNTER_FILE_CREATE_FAILED', '无法打开进程计数文件.');
		define('SESSION_COUNTER_FILE_WRITE_FAILED', '无法读写进程计数文件.');
	//88888888888   Session   888888888888888888888
	
	//88888888888   Below for Text Editor   888888888888888888888
		define('TXT_FILE_NOT_FOUND', '文件不存在.');
		define('TXT_EXT_NOT_SELECTED', '请选择文件扩展名');
		define('TXT_DEST_FOLDER_NOT_SELECTED', '请选择目标文件夹');
		define('TXT_UNKNOWN_REQUEST', '未知的请求.');
		define('TXT_DISALLOWED_EXT', '不允许创建此类文件.');
		define('TXT_FILE_EXIST', '文件已存在.');
		define('TXT_FILE_NOT_EXIST', '指定的文件不存在.');
		define('TXT_CREATE_FAILED', '无法创建新文件.');
		define('TXT_CONTENT_WRITE_FAILED', '无法写入内容.');
		define('TXT_FILE_OPEN_FAILED', '无法打开所选择的文件');
		define('TXT_CONTENT_UPDATE_FAILED', '无法更新文件内容');
		define('TXT_SAVE_AS_ERR_NAME_INVALID', '文件名只能含有英文字母,数字, - 和 _');
	//88888888888   Above for Text Editor   888888888888888888888
	
	
?>