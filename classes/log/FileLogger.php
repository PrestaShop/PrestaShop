<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class FileLoggerCore extends AbstractLogger
{
    /**
     * @var string
     */
    protected $filename = '';

    /**
     * Write the message in the log file.
     *
     * @param string $message
     * @param int $level
     *
     * @return bool
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
     *
     * @return void
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
     * @return string
     */
    public function getFilename()
    {
        if (empty($this->filename)) {
            throw new PrestaShopException('Filename is empty.');
        }

        return $this->filename;
    }
}
