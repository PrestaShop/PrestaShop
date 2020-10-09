<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class FileLoggerCore extends AbstractLogger
{
    protected $filename = '';

    /**
     * Write the message in the log file.
     *
     * @param string message
     * @param level
     */
    protected function logMessage($message, $level)
    {
        if (!is_string($message)) {
            $message = print_r($message, true);
        }
        $formatted_message = '*' . $this->level_value[$level] . '* ' . "\tv" . _PS_VERSION_ . "\t" . date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

        return (bool) file_put_contents($this->getFilename(), $formatted_message, FILE_APPEND);
    }

    /**
     * Check if the specified filename is writable and set the filename.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        if (is_writable(dirname($filename))) {
            $this->filename = $filename;
        } else {
            die('Directory ' . dirname($filename) . ' is not writable');
        }
    }

    /**
     * Log the message.
     *
     * @param string message
     * @param level
     */
    public function getFilename()
    {
        if (empty($this->filename)) {
            die(Tools::displayError('Filename is empty.'));
        }

        return $this->filename;
    }
}
