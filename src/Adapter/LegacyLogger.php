<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Monolog\Logger;

/**
 * Description of Logger
 *
 * @author thomas
 */
class LegacyLogger
{
    public function add($message, $severity = Logger::DEBUG, $error_code = null, $object_type = null, $object_id = null, $allow_duplicate = false, $id_employee = null)
    {
        switch ($severity) {
            case Logger::EMERGENCY:
            case Logger::ALERT:
            case Logger::CRITICAL:
                $pslevel = 4;
                break;
            case Logger::ERROR:
                $pslevel = 3;
                break;
            case Logger::WARNING:
                $pslevel = 2;
                break;
            case Logger::NOTICE:
            case Logger::INFO:
            case Logger::DEBUG:
                $pslevel = 1;
                break;
        }
        \PrestaShopLoggerCore::addLog($message, $pslevel, $error_code, $object_type, $object_id, $allow_duplicate, $id_employee);
    }
}
