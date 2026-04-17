<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
abstract class AbstractLoggerCore implements PrestaShopLoggerInterface
{
    public $level;
    protected $level_value = [
        0 => 'DEBUG',
        1 => 'INFO',
        2 => 'WARNING',
        3 => 'ERROR',
    ];

    public function __construct($level = self::INFO)
    {
        if (array_key_exists((int) $level, $this->level_value)) {
            $this->level = $level;
        } else {
            $this->level = self::INFO;
        }
    }

    /**
     * Log the message.
     *
     * @param string $message
     * @param int $level
     */
    abstract protected function logMessage($message, $level);

    /**
     * Check the level and log the message if needed.
     *
     * @param string $message
     * @param int $level
     */
    public function log($message, $level = self::DEBUG)
    {
        if ($level >= $this->level) {
            $this->logMessage($message, $level);
        }

        Hook::exec(
            'actionLoggerLogMessage',
            [
                'message' => $message,
                'level' => $level,
                'isLogged' => $level >= $this->level,
            ]
        );
    }

    /**
     * Log a debug message.
     *
     * @param string $message
     */
    public function logDebug($message)
    {
        $this->log($message, self::DEBUG);
    }

    /**
     * Log an info message.
     *
     * @param string $message
     */
    public function logInfo($message)
    {
        $this->log($message, self::INFO);
    }

    /**
     * Log a warning message.
     *
     * @param string $message
     */
    public function logWarning($message)
    {
        $this->log($message, self::WARNING);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     */
    public function logError($message)
    {
        $this->log($message, self::ERROR);
    }
}
