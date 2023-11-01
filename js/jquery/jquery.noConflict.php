<?php

header('content-type: application/x-javascript');
if (isset($_GET['version']) && preg_match('/^([0-9\.]+)$/Ui', $_GET['version'])) {
    echo 'var $j'.str_replace('.', '', $_GET['version']).' = jQuery.noConflict(true);';
}
