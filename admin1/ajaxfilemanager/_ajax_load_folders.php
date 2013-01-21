<?php
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
?>
<select class="input inputSearch" name="search_folder" id="search_folder">
	<?php 
	
					foreach(getFolderListing(CONFIG_SYS_ROOT_PATH) as $k=>$v)
					{
						?>
      <option value="<?php echo $v; ?>" ><?php echo shortenFileName($k, 30); ?></option>
      <?php 
					}
		
				?>            	
</select>