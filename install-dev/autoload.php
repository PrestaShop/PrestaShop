<?php

spl_autoload_register(function ($className) {
    if (0 === strpos($className, 'InstallControllerConsole')) {
        $fileName = strtolower(str_replace('InstallControllerConsole', '', $className));
        require_once __DIR__.'/controllers/console/' . $fileName . '.php';
    }
    if (0 === strpos($className, 'InstallControllerHttp')) {
        $fileName = strtolower(str_replace('InstallControllerHttp', '', $className));
        require_once __DIR__.'/controllers/http/' . $fileName . '.php';
    }
    if (file_exists(__DIR__.'/classes/' . $className . '.php')) {
        require_once __DIR__.'/classes/' . $className . '.php';
    }
});
