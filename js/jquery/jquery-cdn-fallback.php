<?php 
header('content-type: application/x-javascript');

$version = empty($_GET['version']) ? '1.11.0' : htmlspecialchars($_GET['version']);

echo 'if (!window.jQuery) {
	document.write(\'<script src=\"/js/jquery/jquery-'.$version.'.min.js'.'\"><\/script>\');
}';
