<?php

/**
 * This class manage upload, with use of the JUpload applet. It's both a sample to show how to use the applet, and
 * a class you can use directly into your own application.
 *
 * Recommandation: Don't update its code !
 *
 * By doing this, you'll be able to reuse directly any update coming from the JUpload project, instead of reporting your
 * modifications into any new version of this class. This guarantees you that your project can use the last version of
 * JUpload, without any code modification. We work so that the applet behavior remains unchanged, but from time to time,
 * a change can appear.
 *
 * Sample:
 * - See the index.php samples, in the same folder.
 *
 * Notes:
 * - maxChunkSize: this class use a default maxChunkSize of 500K (or less, depending on the script max size). This allows
 * upload of FILES OF ANY SIZE on quite all ISP hosting. If it's too big for you (the max upload size of your ISP is less
 * than 500K), or if you want no chunk at all, you can, of course, override this default value.
 *
 *
 *
 * Parameters:
 * - $appletparams contains a map for applet parameters: key is the applet parameter name. The value is the value to transmit
 * 		to the applet. See the applet documentation for information on all applet parameters.
 * - $classparams contains the parameter specific for the JUpload class below. Here are the main class parameters:
 * 		- demo_mode. Files are uploaded to the server, but not stored on its hard drive. That is: you can simulate the global
 * 		behavior, but won't consume hard drive space. This mode is used on sourceforge web site.
 *
 *
 * Output generated for uploaded files:
 * - $files is an array of array. This can be managed by (a) the function given in the callbackAfterUploadManagement class
 * 		parameter, or (b) within the page whose URL is given in the afterUploadURL applet parameter, or (c) you can Extend the
 * 		class and redeclare defaultAfterUploadManagement() to your needs.
 * 	See the defaultAfterUploadManagement() for a sample on howto manage this array.
 *
 *   This array contains:
 *     - One entry per file. Each entry is an array, that contains all files properties, stored as $key=>$value.
 * 		The available keys are:
 * 		  - name: the filename, as it is now stored on the system.
 * 		  - size: the file size
 * 		  - path: the absolute path, where the file has been stored.
 * 			- fullName: the canonical file name (i.e. including the absolute path)
 * 		  - md5sum: the md5sum of the file, if further control is needed.
 * 			- mimetype: the calculated mime type of the file
 * 		  - If the formData applet parameter is used: all attributes (key and value) uploaded by the applet, are put here,
 * 			repeated for each file.
 *
 * 		Note: if you are using a callback function (i.e. callbackAfterUploadManagement) and you do not see a global 'object' you
 * 					are expecting then it might have been destroyed by PHP - c.f. http://bugs.php.net/bug.php?id=39693
 *
 */

class JUpload {

	var $appletparams;
	var $classparams;
	var $files;

	public function JUpload($appletparams = array(), $classparams = array()) {
		if (gettype($classparams) != 'array')
		$this->abort('Invalid type of parameter classparams: Expecting an array');
		if (gettype($appletparams) != 'array')
		$this->abort('Invalid type of parameter appletparams: Expecting an array');

		// set some defaults for the applet params
		if (!isset($appletparams['afterUploadURL']))
		$appletparams['afterUploadURL'] = $_SERVER['PHP_SELF'] . '?afterupload=1';
		if (!isset($appletparams['name']))
		$appletparams['name'] = 'JUpload';
		if (!isset($appletparams['archive']))
		$appletparams['archive'] = 'wjhk.jupload.jar';
		if (!isset($appletparams['code']))
		$appletparams['code'] = 'wjhk.jupload2.JUploadApplet';
		if (!isset($appletparams['debugLevel']))
		$appletparams['debugLevel'] = 0;
		if (!isset($appletparams['httpUploadParameterType']))
		$appletparams['httpUploadParameterType'] = 'array';
		if (!isset($appletparams['showLogWindow']))
		$appletparams['showLogWindow'] = ($appletparams['debugLevel'] > 0) ? 'true' : 'false';
		if (!isset($appletparams['width']))
		$appletparams['width'] = 640;
		if (!isset($appletparams['height']))
		$appletparams['height'] = ($appletparams['showLogWindow'] == 'true') ? 500 : 300;
		if (!isset($appletparams['mayscript']))
		$appletparams['mayscript'] = 'true';
		if (!isset($appletparams['scriptable']))
		$appletparams['scriptable'] = 'false';
		//if (!isset($appletparams['stringUploadSuccess']))
		$appletparams['stringUploadSuccess'] = 'SUCCESS';
		//if (!isset($appletparams['stringUploadError']))
		$appletparams['stringUploadError'] = 'ERROR: (.*)';
		$maxpost = $this->tobytes(ini_get('post_max_size'));
		$maxmem = $this->tobytes(ini_get('memory_limit'));
		$maxfs = $this->tobytes(ini_get('upload_max_filesize'));
		$obd = ini_get('open_basedir');
		if (!isset($appletparams['maxChunkSize'])) {
			$maxchunk = ($maxpost < $maxmem) ? $maxpost : $maxmem;
			$maxchunk = ($maxchunk < $maxfs) ? $maxchunk : $maxfs;
			$maxchunk /= 4;
			$optchunk = (500000 > $maxchunk) ? $maxchunk : 500000;
			$appletparams['maxChunkSize'] = $optchunk;
		}
		$appletparams['maxChunkSize'] = $this->tobytes($appletparams['maxChunkSize']);
		if (!isset($appletparams['maxFileSize']))
		$appletparams['maxFileSize'] = $maxfs;
		$appletparams['maxFileSize'] = $this->tobytes($appletparams['maxFileSize']);
		if (isset($classparams['errormail'])) {
			$appletparams['urlToSendErrorTo'] = $_SERVER["PHP_SELF"] . '?errormail';
		}

		// Same for class parameters
		if (!isset($classparams['demo_mode']))
		$classparams['demo_mode'] = false;
		if ($classparams['demo_mode']) {
			$classparams['create_destdir'] = false;
			$classparams['allow_subdirs'] = true;
			$classparams['allow_zerosized'] = true;
			$classparams['duplicate'] = 'overwrite';
		}
		if (!isset($classparams['debug_php']))											// set true to log some messages in PHP log
		$classparams['debug_php'] = false;
		if (!isset($this->classparams['allowed_mime_types']))				// array of allowed MIME type
		$classparams['allowed_mime_types'] = 'all';
		if (!isset($this->classparams['allowed_file_extensions'])) 	// array of allowed file extensions
		$classparams['allowed_file_extensions'] = 'all';
		if (!isset($classparams['verbose_errors']))						// shouldn't display server info on a production site!
		$classparams['verbose_errors'] = true;
		if (!isset($classparams['session_regenerate']))
		$classparams['session_regenerate'] = false;
		if (!isset($classparams['create_destdir']))
		$classparams['create_destdir'] = true;
		if (!isset($classparams['allow_subdirs']))
		$classparams['allow_subdirs'] = false;
		if (!isset($classparams['spaces_in_subdirs']))
		$classparams['spaces_in_subdirs'] = false;
		if (!isset($classparams['allow_zerosized']))
		$classparams['allow_zerosized'] = false;
		if (!isset($classparams['duplicate']))
		$classparams['duplicate'] = 'rename';
		if (!isset($classparams['dirperm']))
		$classparams['dirperm'] = 0755;
		if (!isset($classparams['fileperm']))
		$classparams['fileperm'] = 0644;
		if (!isset($classparams['destdir'])) {
			if ($obd != '')
			$classparams['destdir'] = $obd;
			else
			$classparams['destdir'] = '/var/tmp/jupload_test';
		}else{
			$classparams['destdir']=str_replace('~',' ',$classparams['destdir']);
		}
		if ($classparams['create_destdir']) {
			$_umask = umask(0); 	// override the system mask
			@mkdir($classparams['destdir'], $classparams['dirperm']);
			umask($_umask);
		}
		if (!is_dir($classparams['destdir']) && is_writable($classparams['destdir']))
		$this->abort('Destination dir not accessible');
		if (!isset($classparams['tmp_prefix']))
		$classparams['tmp_prefix'] = 'jutmp.';
		if (!isset($classparams['var_prefix']))
		$classparams['var_prefix'] = 'juvar.';
		if (!isset($classparams['jscript_wrapper']))
		$classparams['jscript_wrapper'] = 'JUploadSetProperty';
		if (!isset($classparams['tag_jscript']))
		$classparams['tag_jscript'] = '<!--JUPLOAD_JSCRIPT-->';
		if (!isset($classparams['tag_applet']))
		$classparams['tag_applet'] = '<!--JUPLOAD_APPLET-->';
		if (!isset($classparams['tag_flist']))
		$classparams['tag_flist'] = '<!--JUPLOAD_FILES-->';
		if (!isset($classparams['http_flist_start']))
		$classparams['http_flist_start'] =
            		"<table border='1'><TR><TH>Filename</TH><TH>file size</TH><TH>Relative path</TH><TH>Full name</TH><TH>md5sum</TH><TH>Specific parameters</TH></TR>";
		if (!isset($classparams['http_flist_end']))
		$classparams['http_flist_end'] = "</table>\n";
		if (!isset($classparams['http_flist_file_before']))
		$classparams['http_flist_file_before'] = "<tr><td>";
		if (!isset($classparams['http_flist_file_between']))
		$classparams['http_flist_file_between'] = "</td><td>";
		if (!isset($classparams['http_flist_file_after']))
		$classparams['http_flist_file_after'] = "</td></tr>\n";

		$this->appletparams = $appletparams;
		$this->classparams = $classparams;
		$this->page_start();
	}

	/**
	 * Return an array of uploaded files * The array contains: name, size, tmp_name, error,
	 * relativePath, md5sum, mimetype, fullName, path
	 */
	public function uploadedfiles() {
		return $this->files;
	}

	/**
	 * Log a message on the current output, as a HTML comment.
	 */
	protected function logDebug($function, $msg, $htmlComment=true) {
		$output = "[DEBUG] [$function] $msg";
		if ($htmlComment) {
			echo("<!-- $output -->\r\n");
		} else {
			echo("$output\r\n");
		}
	}

	/**
	 * Log a message to the PHP log.
	 * Declared "protected" so it may be Extended if you require customised logging (e.g. particular log file location).
	 */
	protected function logPHPDebug($function, $msg) {
		if ($this->classparams['debug_php'] === true) {
			$output = "[DEBUG] [$function] ".$this->arrayexpand($msg);
			error_log($output);
		}
	}

	private function arrayexpand($array) {
		$output = '';
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				$output .= "\n ".$key.' => '.$this->arrayexpand($value);
			}
		} else {
			$output .= $array;
		}
		return $output;
	}


	/**
	 * Convert a value ending in 'G','M' or 'K' to bytes
	 *
	 */
	private function tobytes($val) {
		$val = trim($val);
		$last = fix_strtolower($val{strlen($val)-1});
		switch($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}

	/**
	 * Build a string, containing a javascript wrapper function
	 * for setting applet properties via JavaScript. This is necessary,
	 * because we use the "modern" method of including the applet (using
	 * <object> resp. <embed> tags) in order to trigger automatic JRE downloading.
	 * Therefore, in Netscape-like browsers, the applet is accessible via
	 * the document.embeds[] array while in others, it is accessible via the
	 * document.applets[] array.
	 *
	 * @return A string, containing the necessary wrapper function (named JUploadSetProperty)
	 */
	private function str_jsinit() {
		$N = "\n";
		$name = $this->appletparams['name'];
		$ret = '<script type="text/javascript">'.$N;
		$ret .= '<!--'.$N;
		$ret .= 'function '.$this->classparams['jscript_wrapper'].'(name, value) {'.$N;
		$ret .= '  document.applets["'.$name.'"] == null || document.applets["'.$name.'"].setProperty(name,value);'.$N;
		$ret .= '  document.embeds["'.$name.'"] == null || document.embeds["'.$name.'"].setProperty(name,value);'.$N;
		$ret .= '}'.$N;
		$ret .= '//-->'.$N;
		$ret .= '</script>';
		return $ret;
	}

	/**
	 * Build a string, containing the applet tag with all parameters.
	 *
	 * @return A string, containing the applet tag
	 */
	private function str_applet() {
		$N = "\n";
		$params = $this->appletparams;
		// return the actual applet tag
		$ret = '<object classid = "clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"'.$N;
		$ret .= '  codebase = "http://java.sun.com/update/1.5.0/jinstall-1_5-windows-i586.cab#Version=5,0,0,3"'.$N;
		$ret .= '  width = "'.$params['width'].'"'.$N;
		$ret .= '  height = "'.$params['height'].'"'.$N;
		$ret .= '  name = "'.$params['name'].'">'.$N;
		foreach ($params as $key => $val) {
			if ($key != 'width' && $key != 'height')
			$ret .= '  <param name = "'.$key.'" value = "'.$val.'" />'.$N;
		}
		$ret .= '  <comment>'.$N;
		$ret .= '    <embed'.$N;
		$ret .= '      type = "application/x-java-applet;version=1.5"'.$N;
		foreach ($params as $key => $val)
		$ret .= '      '.$key.' = "'.$val.'"'.$N;
		$ret .= '      pluginspage = "http://java.sun.com/products/plugin/index.html#download">'.$N;
		$ret .= '      <noembed>'.$N;
		$ret .= '        Java 1.5 or higher plugin required.'.$N;
		$ret .= '      </noembed>'.$N;
		$ret .= '    </embed>'.$N;
		$ret .= '  </comment>'.$N;
		$ret .= '</object>';
		return $ret;
	}

	private function abort($msg = '') {
		$this->cleanup();
		if ($msg != '')
		die(str_replace('(.*)',$msg,$this->appletparams['stringUploadError'])."\n");
		exit;
	}

	private function warning($msg = '') {
		$this->cleanup();
		if ($msg != '')
		echo('WARNING: '.$msg."\n");
		echo $this->appletparams['stringUploadSuccess']."\n";
		exit;
	}

	private function cleanup() {
		// remove all uploaded files of *this* request
		if (isset($_FILES)) {
			foreach ($_FILES as $key => $val)
			@unlink($val['tmp_name']);
		}
		// remove accumulated file, if any.
		@unlink($this->classparams['destdir'].'/'.$this->classparams['tmp_prefix'].session_id());
		@unlink($this->classparams['destdir'].'/'.$this->classparams['tmp_prefix'].'tmp'.session_id());
		// reset session var
		$_SESSION[$this->classparams['var_prefix'].'size'] = 0;
		return;
	}

	private function mkdirp($path) {
		// create subdir (hierary) below destdir;
		$dirs = explode('/', $path);
		$path = $this->classparams['destdir'];
		foreach ($dirs as $dir) {
			$path .= '/'.$dir;
			if (!file_exists($path)) {  // @ does NOT always supress the error!
				$_umask = umask(0); 	// override the system mask
				@mkdir($path, $this->classparams['dirperm']);
				umask($_umask);
			}
		}
		if (!is_dir($path) && is_writable($path))
		$this->abort('Destination dir not accessible');
	}

	/**
	 * This method:
	 * - Replaces some potentially dangerous characters by '_' (in the given name an relative path)
	 * - Checks if a files of the same name already exists.
	 * 		- If no: no problem.
	 * 		- If yes, and the duplicate class param is set to rename, the file is renamed.
	 * 		- If yes, and the duplicate class param is set to overwrite, the file is not renamed. The existing one will be erased.
	 * 		- If yes, and the duplicate class param is set to reject, an error is thrown.
	 */
	private function dstfinal(&$name, &$subdir) {
		$name = preg_replace('![`$\\\\/|]!', '_', $name);
		if ($this->classparams['allow_subdirs'] && ($subdir != '')) {
			$subdir = trim(preg_replace('!\\\\!','/',$subdir),'/');
			$subdir = preg_replace('![`$|]!', '_', $subdir);
			if (!$this->classparams['spaces_in_subdirs']) {
				$subdir = str_replace(' ','_',$subdir);
			}
			// recursively create subdir
			if (!$this->classparams['demo_mode'])
			$this->mkdirp($subdir);
			// append a slash
			$subdir .= '/';
		} else {
			$subdir = '';
		}
		$ret = $this->classparams['destdir'].'/'.$subdir.$name;
		if (file_exists($ret)) {
			if ($this->classparams['duplicate'] == 'overwrite') {
				return $ret;
			}
			if ($this->classparams['duplicate'] == 'reject') {
				$this->abort('A file with the same name already exists');
			}
			if ($this->classparams['duplicate'] == 'warning') {
				$this->warning("File $name already exists - rejected");
			}
			if ($this->classparams['duplicate'] == 'rename') {
				$cnt = 1;
				$dir = $this->classparams['destdir'].'/'.$subdir;
				$ext = strrchr($name, '.');
				if ($ext) {
					$nameWithoutExtension = substr($name, 0, strlen($name) - strlen($ext));
				} else {
					$ext = '';
					$nameWithoutExtension = $name;
				}

				$rtry = $dir.$nameWithoutExtension.'_'.$cnt.$ext;
				while (file_exists($rtry)) {
					$cnt++;
					$rtry = $dir.$nameWithoutExtension.'._'.$cnt.$ext;
				}
				//We store the result name in the byReference name parameter.
				$name = $nameWithoutExtension.'_'.$cnt.$ext;
				$ret = $rtry;
			}
		}
		return $ret;
	}

	/**
	 * Example function to process the files uploaded.  This one simply displays the files' data.
	 *
	 */
	public function defaultAfterUploadManagement() {
		$flist = '[defaultAfterUploadManagement] Nb uploaded files is: ' . sizeof($this->files);
		$flist = $this->classparams['http_flist_start'];
		foreach ($this->files as $f) {
			//$f is an array, that contains all info about the uploaded file.
			$this->logDebug('defaultAfterUploadManagement', "  Reading file ${f['name']}");
			$flist .= $this->classparams['http_flist_file_before'];
			$flist .= $f['name'];
			$flist .= $this->classparams['http_flist_file_between'];
			$flist .= $f['size'];
			$flist .= $this->classparams['http_flist_file_between'];
			$flist .= $f['relativePath'];
			$flist .= $this->classparams['http_flist_file_between'];
			$flist .= $f['fullName'];
			$flist .= $this->classparams['http_flist_file_between'];
			$flist .= $f['md5sum'];
			$addBR = false;
			foreach ($f as $key=>$value) {
				//If it's a specific key, let's display it:
				if ($key != 'name' && $key != 'size' && $key != 'relativePath' && $key != 'fullName' && $key != 'md5sum') {
					if ($addBR) {
						$flist .= "<br>";
					} else {
						// First line. We must add a new 'official' list separator.
						$flist .= $this->classparams['http_flist_file_between'];
						$addBR = true;
					}
					$flist .= "$key => $value";
				}
			}
			$flist .= $this->classparams['http_flist_file_after'];
	}
	$flist .= $this->classparams['http_flist_end'];

	return $flist;
}

/**
 * Generation of the applet tag, and necessary things around (js content). Insertion of this into the content of the
 * page.
 * See the tag_jscript and tag_applet class parameters.
 */
private function generateAppletTag($str) {
	$this->logDebug('generateAppletTag', 'Entering function');
	$str = preg_replace('/'.$this->classparams['tag_jscript'].'/', $this->str_jsinit(), $str);
	return preg_replace('/'.$this->classparams['tag_applet'].'/', $this->str_applet(), $str);
}

/**
 * This function is called when constructing the page, when we're not reveiving uploaded files. It 'just' construct
 * the applet tag, by calling the relevant function.
 *
 * This *must* be public, because it is called from PHP's output buffering
 */
public function interceptBeforeUpload($str) {
	$this->logDebug('interceptBeforeUpload', 'Entering function');
	return $this->generateAppletTag($str);
}

/**
 * This function displays the uploaded files description in the current page (see tag_flist class parameter)
 *
 * This *must* be public, because it is called from PHP's output buffering.
 */
public function interceptAfterUpload($str) {
	$this->logDebug('interceptAfterUpload', 'Entering function');
	$this->logPHPDebug('interceptAfterUpload', $this->files);

	if (count($this->files) > 0) {
		if (isset($this->classparams['callbackAfterUploadManagement'])) {
			$this->logDebug('interceptAfterUpload', 'Before call of ' .$this->classparams['callbackAfterUploadManagement']);
			$strForFListContent = call_user_func($this->classparams['callbackAfterUploadManagement'], $this, $this->files);
		} else {
			$strForFListContent = $this->defaultAfterUploadManagement();
		}
		$str = preg_replace('/'.$this->classparams['tag_flist'].'/', $strForFListContent, $str);
	}
	return $this->generateAppletTag($str);
}

/**
 * This method manages the receiving of the debug log, when an error occurs.
 */
private function receive_debug_log() {
	// handle error report
	if (isset($_POST['description']) && isset($_POST['log'])) {
		$msg = $_POST['log'];
		mail($this->classparams['errormail'], $_POST['description'], $msg);
	} else {
		if (isset($_SERVER['SERVER_ADMIN']))
		mail($_SERVER['SERVER_ADMIN'], 'Empty jupload error log',
                    'An empty log has just been posted.');
		$this->logPHPDebug('receive_debug_log', 'Empty error log received');
	}
	exit;
}

/**
 * This method is the heart of the system. It manage the files sent by the applet, check the incoming parameters (md5sum) and
 * reconstruct the files sent in chunk mode.
 *
 * The result is stored in the $files array, and can then be managed by the function given in the callbackAfterUploadManagement
 * class parameter, or within the page whose URL is given in the afterUploadURL applet parameter.
 * Or you can Extend the class and redeclare defaultAfterUploadManagement() to your needs.
 */
private function receive_uploaded_files() {
	$this->logDebug('receive_uploaded_files', 'Entering POST management');

	if (session_id() == '') {
		session_start();
	}
	// we check for the session *after* handling possible error log
	// because an error could have happened because the session-id is missing.
	if (!isset($_SESSION[$this->classparams['var_prefix'].'size'])) {
		$this->abort('Invalid session (in afterupload, POST, check of size)');
	}
	if (!isset($_SESSION[$this->classparams['var_prefix'].'files'])) {
		$this->abort('Invalid session (in afterupload, POST, check of files)');
	}
	$this->files = $_SESSION[$this->classparams['var_prefix'].'files'];
	if (!is_array($this->files)) {
		$this->abort('Invalid session (in afterupload, POST, is_array(files))');
	}
	if ($this->appletparams['sendMD5Sum'] == 'true'  &&  !isset($_POST['md5sum'])) {
		$this->abort('Required POST variable md5sum is missing');
	}
	$cnt = 0;
	foreach ($_FILES as $key => $value) {
		//Let's read the $_FILES data
		if (isset($files_data)) {
			unset($files_data);
		}
		$jupart			= (isset($_POST['jupart']))		 		? (int)$_POST['jupart']		: 0;
		$jufinal		= (isset($_POST['jufinal']))	 		? (int)$_POST['jufinal']	: 1;
		$relpaths		= (isset($_POST['relpathinfo'])) 	? $_POST['relpathinfo']		: null;
		$md5sums		= (isset($_POST['md5sum']))				? $_POST['md5sum']				: null;
		$mimetypes 	= (isset($_POST['mimetype'])) 	 	? $_POST['mimetype'] 			: null;
		//$relpaths = (isset($_POST["relpathinfo$cnt"])) ? $_POST["relpathinfo$cnt"] : null;
		//$md5sums = (isset($_POST["md5sum$cnt"])) ? $_POST["md5sum$cnt"] : null;

		if (gettype($relpaths) == 'string') {
			$relpaths = array($relpaths);
		}
		if (gettype($md5sums) == 'string') {
			$md5sums = array($md5sums);
		}
		if ($this->appletparams['sendMD5Sum'] == 'true'  && !is_array($md5sums)) {
			$this->abort('Expecting an array of MD5 checksums');
		}
		if (!is_array($relpaths)) {
			$this->abort('Expecting an array of relative paths');
		}
		if (!is_array($mimetypes)) {
			$this->abort('Expecting an array of MIME types');
		}
		// Check the MIME type (note: this is easily forged!)
		if (isset($this->classparams['allowed_mime_types']) && is_array($this->classparams['allowed_mime_types'])) {
			if (!in_array($mimetypes[$cnt], $this->classparams['allowed_mime_types'])) {
				$this->abort('MIME type '.$mimetypes[$cnt].' not allowed');
			}
		}
		if (isset($this->classparams['allowed_file_extensions']) && is_array($this->classparams['allowed_file_extensions'])) {
			$fileExtension = substr(strrchr($value['name'][$cnt], "."), 1);
			if (!in_array($fileExtension, $this->classparams['allowed_file_extensions'])) {
				$this->abort('File extension '.$fileExtension.' not allowed');
			}
		}

		$dstdir = $this->classparams['destdir'];
		$dstname = $dstdir.'/'.$this->classparams['tmp_prefix'].session_id();
		$tmpname = $dstdir.'/'.$this->classparams['tmp_prefix'].'tmp'.session_id();

		// Controls are now done. Let's store the current uploaded files properties in an array, for future use.
		$files_data['name']					= $value['name'][$cnt];
		$files_data['size']					= 'not calculated yet';
		$files_data['tmp_name']			= $value['tmp_name'][$cnt];
		$files_data['error']    		= $value['error'][$cnt];
		$files_data['relativePath'] = $relpaths[$cnt];
		$files_data['md5sum']  			= $md5sums[$cnt];
		$files_data['mimetype']  		= $mimetypes[$cnt];

		if (!move_uploaded_file($files_data['tmp_name'], $tmpname)) {
			if ($classparams['verbose_errors']) {
				$this->abort("Unable to move uploaded file (from ${files_data['tmp_name']} to $tmpname)");
		} else {
			trigger_error("Unable to move uploaded file (from ${files_data['tmp_name']} to $tmpname)",E_USER_WARNING);
			$this->abort("Unable to move uploaded file");
	}
}

// In demo mode, no file storing is done. We just delete the newly uploaded file.
if ($this->classparams['demo_mode']) {
	if ($jufinal || (!$jupart)) {
		if ($jupart) {
			$files_data['size']		= ($jupart-1) * $this->appletparams['maxChunkSize'] + filesize($tmpname);
		} else {
			$files_data['size']		= filesize($tmpname);
		}
		$files_data['fullName']	= 'Demo mode<BR>No file storing';
		array_push($this->files, $files_data);
	}
	unlink($tmpname);
	$cnt++;
	continue;
}
//If we get here, the upload is a real one (no demo)
if ($jupart) {
	// got a chunk of a multi-part upload
	$len = filesize($tmpname);
	$_SESSION[$this->classparams['var_prefix'].'size'] += $len;
	if ($len > 0) {
		$src = fopen($tmpname, 'rb');
		$dst = fopen($dstname, ($jupart == 1) ? 'wb' : 'ab');
		while ($len > 0) {
			$rlen = ($len > 8192) ? 8192 : $len;
			$buf = fread($src, $rlen);
			if (!$buf) {
				fclose($src);
				fclose($dst);
				unlink($dstname);
				$this->abort('read IO error');
			}
			if (!fwrite($dst, $buf, $rlen)) {
				fclose($src);
				fclose($dst);
				unlink($dstname);
				$this->abort('write IO error');
			}
			$len -= $rlen;
		}
		fclose($src);
		fclose($dst);
		unlink($tmpname);
	}
	if ($jufinal) {
		// This is the last chunk. Check total lenght and
		// rename it to it's final name.
		$dlen = filesize($dstname);
		if ($dlen != $_SESSION[$this->classparams['var_prefix'].'size'])
		$this->abort('file size mismatch');
		if ($this->appletparams['sendMD5Sum'] == 'true' ) {
			if ($md5sums[$cnt] != md5_file($dstname))
			$this->abort('MD5 checksum mismatch');
		}
		// remove zero sized files
		if (($dlen > 0) || $this->classparams['allow_zerosized']) {
			$dstfinal = $this->dstfinal($files_data['name'],$files_data['relativePath']);
			if (!rename($dstname, $dstfinal))
			$this->abort('rename IO error');
			$_umask = umask(0); 	// override the system mask
			if (!chmod($dstfinal, $this->classparams['fileperm']))
				$this->abort('chmod IO error');
			umask($_umask);
			$files_data['size']		= filesize($dstfinal);
			$files_data['fullName']	= $dstfinal;
			$files_data['path']	= fix_dirname($dstfinal);
			array_push($this->files, $files_data);
		} else {
			unlink($dstname);
		}
		// reset session var
		$_SESSION[$this->classparams['var_prefix'].'size'] = 0;
	}
} else {
	// Got a single file upload. Trivial.
	if ($this->appletparams['sendMD5Sum'] == 'true' ) {
		if ($md5sums[$cnt] != md5_file($tmpname))
			$this->abort('MD5 checksum mismatch');
	}
	$dstfinal = $this->dstfinal($files_data['name'],$files_data['relativePath']);
	if (!rename($tmpname, $dstfinal))
	$this->abort('rename IO error');
	$_umask = umask(0); 	// override the system mask
	if (!chmod($dstfinal, $this->classparams['fileperm']))
		$this->abort('chmod IO error');
	umask($_umask);
	$files_data['size']		= filesize($dstfinal);
	$files_data['fullName']	= $dstfinal;
	$files_data['path']	= fix_dirname($dstfinal);
	array_push($this->files, $files_data);
}
$cnt++;
}

echo $this->appletparams['stringUploadSuccess']."\n";
$_SESSION[$this->classparams['var_prefix'].'files'] = $this->files;
session_write_close();
exit;
}

/**
 *
 *
 */
private function page_start() {
	$this->logDebug('page_start', 'Entering function');

	// If the applet checks for the serverProtocol, it issues a HEAD request
	// -> Simply return an empty doc.
	if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
		// Nothing to do

	} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		// A GET request means: return upload page
		$this->logDebug('page_start', 'Entering GET management');

		if (session_id() == '') {
			session_start();
		}
		if (isset($_GET['afterupload'])) {
			$this->logDebug('page_start', 'afterupload is set');
			if (!isset($_SESSION[$this->classparams['var_prefix'].'files'])) {
				$this->abort('Invalid session (in afterupload, GET, check of $_SESSION): files array is not set');
			}
			$this->files = $_SESSION[$this->classparams['var_prefix'].'files'];
			if (!is_array($this->files)) {
				$this->abort('Invalid session (in afterupload, GET, check of is_array(files)): files is not an array');
			}
			// clear session data ready for new upload
			$_SESSION[$this->classparams['var_prefix'].'files'] = array();

			// start intercepting the content of the calling page, to display the upload result.
			ob_start(array(& $this, 'interceptAfterUpload'));

		} else {
			$this->logDebug('page_start', 'afterupload is not set');
			if ($this->classparams['session_regenerate']) {
				session_regenerate_id(true);
			}
			$this->files = array();
			$_SESSION[$this->classparams['var_prefix'].'size'] = 0;
			$_SESSION[$this->classparams['var_prefix'].'files'] = $this->files;
			// start intercepting the content of the calling page, to display the applet tag.
			ob_start(array(& $this, 'interceptBeforeUpload'));
		}

	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// If we got a POST request, this is the real work.
		if (isset($_GET['errormail'])) {
			//Hum, an error occurs on server side. Let's manage the debug log, that we just received.
			$this->receive_debug_log();
		} else {
			$this->receive_uploaded_files();
		}
	}
}
}

// PHP end tag omitted intentionally!!
