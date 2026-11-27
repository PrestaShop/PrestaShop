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
     * Stream wrappers such as php://stderr are accepted as is, so logs can go
     * to the container output when the filesystem is read-only or ephemeral.
     *
     * @param string $filename
     *
     * @return void
     *
     * @throws PrestaShopException when the directory is not writable
     */
    public function setFilename($filename)
    {
        if ($this->isStreamWrapper((string) $filename) || is_writable(dirname($filename))) {
            $this->filename = $filename;

            return;
        }

        throw new PrestaShopException(sprintf('Directory %s is not writable', dirname($filename)));
    }

    private function isStreamWrapper(string $filename): bool
    {
        if (!preg_match('#^([a-z][a-z0-9+.-]*)://#i', $filename, $matches)) {
            return false;
        }

        $scheme = strtolower($matches[1]);

        // file:// still points to the local filesystem, so its directory must be writable
        return $scheme !== 'file' && in_array($scheme, stream_get_wrappers(), true);
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
