<?php

include '../config/config.php';
if(!$java_upload) die('forbidden');
if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbidden');

//Let's load the 'interesting' stuff ...  ;-)
include 'jupload.php';
include '../include/utils.php';

$path=$current_path.$_GET['path'];
$cycle=true;
$max_cycles=50;
$i=0;
while($cycle && $i<$max_cycles){
    $i++;
    if($path==$current_path)  $cycle=false;
    
    if(file_exists($path."config.php")){
	require_once($path."config.php");
	$cycle=false;
    }
    $path=fix_dirname($path)."/";
}

$path="../".$current_path.$_GET['path'];

if(strpos($_GET['path'],'../')!==FALSE || strpos($_GET['path'],'./')!==FALSE || strpos($_GET['path'],'/')===0) die ('path error');

$path=str_replace(' ','~',$path);
////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////   The user callback function, that can be called after upload   ////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * This function will be called, once all files are uploaded, with the list of uploaded files as an argument.
 *
 * Condition to have this function called:
 * - Have the applet parameter afterUploadURL unset in this file. This makes the applet use its default behavior, that is: afterUploadURL is
 *  the current web page, with the ?afterupload=1 parameter added.
 * - Have the class parameter callbackAfterUploadManagement set to 'handle_uploaded_files', name of this callback. You can use any name you want,
 *  but the function must accept one unique parameter: the array that contains the file descriptions.
 *
 * @param $juploadPhpSupportClass The instance of the JUpload PHP class.
 * @param $file The array wich contains info about all uploaded files.
 */
function handle_uploaded_files($juploadPhpSupportClass, $files) {
	return
		"<P>We are in the 'handle_uploaded_files' callback function, in the index.php script. To avoid double coding, we "
		. "just call the default behavior of the JUpload PHP class. Just replace this by your code...</P>"
		. $juploadPhpSupportClass->defaultAfterUploadManagement();
		;

}
////////////////////////////////////////////////////////////////////////////////////////////////////////



//First: the applet parameters
//
// Default value should work nice on most configuration. In this sample, we use some specific parameters, to show
// how to use this array.
// See comment for the parameters used on this demo page.
//
// You can use all applet parameters in this array.
// see all details http://jupload.sourceforge.net/howto-customization.html
//
$appletParameters = array(
		//Default value is ... maximum size for a file on the current FS. 2G is problably too much already.
        'maxFileSize' => $JAVAMaxSizeUpload.'G',
        //
        //In the sourceforge project structure, the applet jar file is one folder below. Default
        //configuration is ok, if wjhk.jupload.jar is in the same folder as the script containing this call.
        'archive' => 'wjhk.jupload.jar',
	'showLogWindow' => 'false',
	'width' => '100%',
	'height' =>'358px',
	'name' => 'No limit Uploader',
	'allowedFileExtensions' => implode('/',$ext),
        //To manage, other jar files, like the ftp jar files if postURL is an FTP URL:
        //'archive' => 'wjhk.jupload.jar,jakarta-commons-oro.jar,jakarta-commons-net.jar',
        //
        //Default afterUploadURL displays the list of uploaded files above the applet (in the <!--JUPLOAD_FILES--> markers, see below)
        //You can use any page you want, to manage the uploaded files. Here is a sample, that also only shows the list of files.
        'afterUploadURL' => 'success.php?path='.$_GET['path'],
        //
        //This demo expects the md5sum to be sent by the applet. But the parameter is not mandatory
        //This value should be set to false (or the line commented), for big files, as md5 calculation
        //may be long  (Note this must be string and *not* boolean true/false)
        'sendMD5Sum' => 'false',
        //
        'debugLevel' => 0 // 100 disables redirect after upload, so we keep it below. This still gives a lot of information, in case of problem.
    );

// for htaccess protected folders 
if((isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] != '') && $_SERVER['PHP_AUTH_USER'] != '' && $_SERVER['PHP_AUTH_USER'] != '') 
{
	$appletParameters['specificHeaders'] = 'Authorization: Basic '.base64_encode($_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW']);
}

//
//Then: the jupload PHP class parameters
$classParameters = array(
		//Files won't be stored on the server. Useful for first tests of the applet behavior ... and sourceforge demo site !
        'demo_mode' => false,
        //
        //Allow creation of subdirectories, when uploading several folders/files (drag and drop a folder on the applet to use it).
        'allow_subdirs' => true,
        //
        // The callbackAfterUploadManagement function will be called, once all files are uploaded, with the list
        //of uploaded files as an argument. See the above sample, and change it according to your needs.
        //'callbackAfterUploadManagement' => 'handle_uploaded_files',
        //
        //I work on windows. The default configuration is /var/tmp/jupload_test
        'destdir' => $path  //Where to store the files on the web
        //'errormail' => 'me@my.domain.org',
    );

////////////////////////////////////////////////////////////////////////////////////////////////////////
// Instantiate and initialize JUpload : integration of the applet in your web site.
$juploadPhpSupportClass = new JUpload($appletParameters, $classParameters);
////////////////////////////////////////////////////////////////////////////////////////////////////////



//Then, a simple HTML page, for the demo
//
// "<!--JUPLOAD_FILES-->" is the tag where the list of uploaded files will be written.
// "<!--JUPLOAD_APPLET-->" is the place where the applet will be integrated, in the web page.
?>
<html>
  <head>
    <!--JUPLOAD_JSCRIPT-->
    <title>JUpload RESPONSIVE filemanager</title>
    <style>
	body{padding:0px; margin:0px;}
    </style>
  </head>
  <body>
    <div align="center"><!--JUPLOAD_FILES--></div>
    <div align="center"><!--JUPLOAD_APPLET--></div>
  </body>
</html>