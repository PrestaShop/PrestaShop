<?php
	/**
	 * class history
	 * this class used to keep records of any changed to uploaded images under a session
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	class History
	{
		var $history = array(); //keep all changes
		var $path = ''; //path to the iamge
		var $session = null;
		/**
		 * constructor
		 *
		 * @param string $path the path to the image 
		 * @param object $session an instance of session class
		 */
		function __construct($path, &$session)
		{
			$this->path = $path;
			$this->session = &$session;
			if(!isset($_SESSION[$this->path]))
			{
				$_SESSION[$this->path] = array();
			}
			
		}
		/**
		 * constructor
		 *
		 * @param string $path the path to the image 
		 * @param object $session an instance of session class
		 */		
		function History($path, &$session)
		{
			$this->__construct($path, $session);
		}

		/**
		 * keep tracks of each changes made to an image
		 *
		 * @param string $key
		 * @param string $info   array('name', 'restorable', 'is_original')
		 */
		function add($info)
		{
			$_SESSION[$this->path][] = $info;
		}
		/**
		 * get the lastest changes for restore
		 *
		 * @return array array('name', 'restorable', 'is_original')
		 */
		function getNumRestorable()
		{
			$output = 0;
			if(isset($_SESSION[$this->path]) && is_array($_SESSION[$this->path]))
			{
				foreach($_SESSION[$this->path] as $k=>$v)
				{
					if(!empty($v['restorable']) && empty($v['is_original']))
					{
						if(file_exists($this->session->getSessionDir() . $v['name']))
						{
							$output++;
						}else 
						{
							
						}
						
					}
				}
			}
			return $output;
		}

		/**
		 * get the path of image which keep the lastest changes
		 *
		 * @return  return empty array when failed
		 */
		function getLastestRestorable()
		{
			if(isset($_SESSION[$this->path]) && is_array($_SESSION[$this->path]) && sizeof($_SESSION[$this->path]))
			{	
				$sessionImages = array_reverse($_SESSION[$this->path], true);
				$lastestKey = '';
				foreach($sessionImages as $k=>$v)
				{
					if($v['restorable'] && empty($v['is_original']) && file_exists($this->session->getSessionDir() . $v['name']))
					{
						return $sessionImages[$k];
					}
				}							
				
			}
			return  array();
			
		}
		/**
		 * get the original image which is kept in the session folder
		 *
		 * @return array
		 */
		function getOriginalImage()
		{
			$outputs = array();
			if(isset($_SESSION[$this->path]) && is_array($_SESSION[$this->path]))
			{
				$sessionImages = array_reverse($_SESSION[$this->path], true);
				foreach($sessionImages as $k=>$v)
				{
					if(!empty($v['is_original']))
					{
						if(file_exists($this->session->getSessionDir() . $v['name']))
						{
							return array('info'=>$_SESSION[$this->path][$k], 'key'=>$k);
						}
						
					}
				}
			}	
			return $outputs;
				
		}
		/**
		 * remove the lastest restorable state
		 *
		 * @return boolean
		 */
		function restore()
		{
			if(isset($_SESSION[$this->path]) && is_array($_SESSION[$this->path]) && sizeof($_SESSION[$this->path]))
			{
				$sessionImages = array_reverse($_SESSION[$this->path], true);
				$lastestKey = '';
				foreach($sessionImages as $k=>$v)
				{
					if($v['restorable'] && empty($v['is_original']))
					{
						unset($_SESSION[$k]);
						return true;
					}
				}
			}
			return false;		
		}

		
		
	}
?>