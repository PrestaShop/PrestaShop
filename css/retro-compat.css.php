<?php

header('content-type: text/css');
$css_folder = __DIR__.'/../js/jquery/';

$css_files = array(
				'datepicker.css' => 
					array('new_file' => $css_folder.'ui/themes/base/jquery.ui.datepicker.css'),
				'fileuploader.css' => 
					array('new_file' => $css_folder.'plugins/ajaxfileupload/jquery.ajaxfileupload.css'),
				'jquery.autocomplete.css' => 
					array('new_file' => $css_folder.'plugins/autocomplete/jquery.autocomplete.css'),
				'jquery.cluetip.css' => 
					array('new_file' => $css_folder.'plugins/cluetip/jquery.cluetip.css'),
				'jquery.fancybox-1.3.4.css' => 
					array('new_file' => $css_folder.'plugins/fancybox/jquery.fancybox.css'),
				'jquery.jgrowl.css'=> 
					array('new_file' => $css_folder.'plugins/jgrowl/jquery.jgrowl.css'),
				'jquery.treeview.css' =>
					array('new_file' => $css_folder.'plugins/treeview-categories/jquery.treeview-categories.css'),
				'jqzoom.css' => 
					array('new_file' => $css_folder.'plugins/jqzoom/jquery.jqzoom.css'),
				'tabpane.css' => 
					array('new_file' => $css_folder.'plugins/tabpane/jquery.tabpane.css'),
				'thickbox.css' => 
					array('new_file' => $css_folder.'plugins/thickbox/jquery.thickbox.css'),
				'jquery.fancybox.css' => 
					array('new_file' => $css_folder.'plugins/fancybox/jquery.fancybox.css'),
				'jquery.autocomplete.css' => 
					array('new_file' => $css_folder.'plugins/autocomplete/jquery.autocomplete.css')
				);
				
				
				

$file = $_GET['file'];

if (!array_key_exists($file, $css_files)) //check if file is a real prestashop native CSS
	die('file_not_found');
else
	$html = file_get_contents($css_files[$file]['new_file']);

if ($file == 'datepicker.css')
{
	$html = file_get_contents($css_folder.'ui/themes/base/jquery.ui.theme.css');
	$html .= file_get_contents($css_folder.'ui/themes/base/jquery.ui.datepicker.css');
	$html = str_replace('url(images', 'url(../ui/themes/base/images', $html);
}	
echo $html ;
