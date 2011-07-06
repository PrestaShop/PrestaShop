<?php

	/**
	 * this class provide a function like session handling engine
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "class.file.php");
class Session 
{
    var $lifeTime;
    var $fp = null;
    var $dir = null;
    var $mTime = null;
    var $sessionDir = null;
    var $sessionFile = null;
    var $ext = '.txt';
    var $gcCounter = 5; //call gc to delete expired session each ten request
		var $gcCounterFileName = 'gc_counter.ajax.php';
		var $gcCounterFile = null;
		var $gcLogFileName = 'gc_log.ajax.php';
		var $gcLogFile = null;
		var $debug = true; //turn it on when you want to see gc log
		
		
    /**
     * constructor
     *
     */
    function __construct()
    {
    	//check if the session folder read and writable
    	/*
	$dir = new file();
	        if(!file_exists(CONFIG_SYS_DIR_SESSION_PATH))
	        {
	           if(!$dir->mkdir(CONFIG_SYS_DIR_SESSION_PATH))
	           {
	              die('Unable to create session folder.');
	           }
	        }
    		if(!$dir->isReadable(CONFIG_SYS_DIR_SESSION_PATH))
    		{
    			die('Permission denied: ' . CONFIG_SYS_DIR_SESSION_PATH . " is not readable.");
    		}    		
    		if(!$dir->isWritable(CONFIG_SYS_DIR_SESSION_PATH))
    		{
    			die('Permission denied: ' . CONFIG_SYS_DIR_SESSION_PATH . " is not writable.");
    		}
    	$this->dir = backslashToSlash(addTrailingSlash(CONFIG_SYS_DIR_SESSION_PATH));
        $this->lifeTime = get_cfg_var("session.gc_maxlifetime");  
        $this->gcCounterFile = $this->dir . $this->gcCounterFileName; 
        $this->gcLogFile = $this->dir  . $this->gcLogFileName;
       	$this->sessionDir = backslashToSlash($this->dir.session_id().DIRECTORY_SEPARATOR);
*/
        $this->init();    	
    }
     /**
     * constructor
     *
     */   
    function Session() 
    {
    		$this->__construct();        
    }
    /**
     * session init
     * @return boolean
     */
    function init() 
    {

        
        
    }
    
    function gc()
    {
     		//init the counter file
        $fp = @fopen($this->gcCounterFile, 'a+');
        if($fp)
        {
        	$count = (int)(fgets($fp, 999999)) + 1;
        	if($count > $this->gcCounter || rand(0, 23) == date('h'))
        	{
        		$this->_gc();
        		$count = 0;
        	}
        	@ftruncate($fp, 0);
        	if(!@fputs($fp, $count))
        	{
        		die(SESSION_COUNTER_FILE_WRITE_FAILED);
        	}
        	@fclose($fp);
        }else 
        {
        	die(SESSION_COUNTER_FILE_CREATE_FAILED);
        }   	
    }


    function _gc() 
    {
			//remove expired file from session folder
	 		$dirHandler = @opendir($this->dir);
	 		$output = '';
	 		$output .= "gc start at " . date('d/M/Y H:i:s') . "\n";
	 		$fo = new file();
			if($dirHandler)
			{
				while(false !== ($file = readdir($dirHandler)))
				{
					if($file != '.' && $file != '..' && $file != $this->gcCounterFileName && $file != $this->gcLogFileName && $file != session_id() )
					{						
						$path=$this->dir.$file;
						$output .= $path ;
						//check if this is a expired session file
						if(filemtime($path) + $this->lifeTime < time())
						{							
							if($fo->delete($path))
							{
								$output .= ' Deleted at ' . date('d/M/Y H:i:s');
							}else 
							{
								$output .= " Failed at " . date('d/M/Y H:i:s');
							}																			
						}
						$output .= "\n";
											
					}
				}
				if($this->debug)
				{
					$this->_log($output);
				}
				
				@closedir($dirHandler);

			} 
			if(CONFIG_SYS_DEMO_ENABLE)
			{
				//remove expired files from uploaded folder
		 		$dirHandler = @opendir(CONFIG_SYS_ROOT_PATH);
		 		$output = '';
		 		$output .= "gc start at " . date('d/M/Y H:i:s') . "\n";
		 		$fo = new file();
				if($dirHandler)
				{
					while(false !== ($file = readdir($dirHandler)))
					{
						if($file != '.' && $file != '..')
						{						
							$path=CONFIG_SYS_ROOT_PATH.$file;
							$output .= $path ;
							//check if this is a expired session file
							if(filemtime($path) + $this->lifeTime < time())
							{							
								if($fo->delete($path))
								{
									$output .= ' Deleted at ' . date('d/M/Y H:i:s');
								}else 
								{
									$output .= " Failed at " . date('d/M/Y H:i:s');
								}																			
							}
							$output .= "\n";
												
						}
					}
					if($this->debug)
					{
						$this->_log($output);
					}
					
					@closedir($dirHandler);
	
				}					
			}
		    
    }
    /**
     * log action taken by the gc
     *
     * @param unknown_type $msg
     */
    function _log($msg)
    {
    	$msg = "<?php die(); ?>\n" . $msg;
    	$fp = @fopen($this->gcLogFile, 'w+');
    	if($fp)
    	{
    		@ftruncate($fp, 0);
    		!@fputs($fp, $msg);
    		@fclose($fp);
    	}
    }
    
    /**
     * get the current session directory
     *
     * @return string return empty if failed
     */
    function getSessionDir()
    {
    	if(!file_exists($this->sessionDir) && !is_dir($this->sessionDir))
    	{
    		$dir = new file();
    		if(!$dir->mkdir($this->sessionDir))
    		{
    			return '';
    		}
    	}else 
    	{
	     	if(!@is_dir($this->sessionDir))
	    	{
	    		return '';
	    	}   		
    	}
    	return $this->sessionDir;
    }
    

    
}
?>