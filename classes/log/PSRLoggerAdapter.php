<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * This class is an adapter if can use PrestaShopLoggerInterface and decorate it into a PSR logger.
 */
class PSRLoggerAdapter implements LoggerInterface
{
    /**
     * @var PrestaShopLoggerInterface
     */
    private $logger;

    private bool $saveMessages = false;
    /**
     * @var array<string, string[]>
     */
    private array $savedMessages;

    public function __construct(PrestaShopLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function emergency($message, array $context = []): void
    {
        $this->logger->logError($message);
        $this->saveMessage(LogLevel::EMERGENCY, $message);
    }

    public function alert($message, array $context = []): void
    {
        $this->logger->logError($message);
        $this->saveMessage(LogLevel::ALERT, $message);
    }

    public function critical($message, array $context = []): void
    {
        $this->logger->logError($message);
        $this->saveMessage(LogLevel::CRITICAL, $message);
    }

    public function error($message, array $context = []): void
    {
        $this->logger->logError($message);
        $this->saveMessage(LogLevel::ERROR, $message);
    }

    public function warning($message, array $context = []): void
    {
        $this->logger->logWarning($message);
        $this->saveMessage(LogLevel::WARNING, $message);
    }

    public function notice($message, array $context = []): void
    {
        $this->logger->logInfo($message);
        $this->saveMessage(LogLevel::NOTICE, $message);
    }

    public function info($message, array $context = []): void
    {
        $this->logger->logInfo($message);
        $this->saveMessage(LogLevel::INFO, $message);
    }

    public function debug($message, array $context = []): void
    {
        $this->logger->logDebug($message);
    }

    public function log($level, $message, array $context = []): void
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::CRITICAL:
            case LogLevel::ALERT:
            case LogLevel::ERROR:
                $legacyLevel = PrestaShopLoggerInterface::ERROR;
                break;
            case LogLevel::WARNING:
                $legacyLevel = PrestaShopLoggerInterface::WARNING;
                break;
            case LogLevel::NOTICE:
            case LogLevel::INFO:
                $legacyLevel = PrestaShopLoggerInterface::INFO;
                break;
            case LogLevel::DEBUG:
            default:
                $legacyLevel = PrestaShopLoggerInterface::DEBUG;
                break;
        }
        $this->logger->log($message, $legacyLevel);
        if ($level == LogLevel::DEBUG) {
            $this->saveMessage($level, $message);
        }
    }

    /**
     * All messages logged after this method is called are stored in a class field.
     */
    public function startSavingMessages(): void
    {
        $this->saveMessages = true;
    }

    /**
     * Stop saving log records and clear the saved records.
     */
    public function stopSavingMessages(): void
    {
        $this->saveMessages = false;
        $this->savedMessages = [];
    }

    public function getAllSavedMessages(): array
    {
        return $this->savedMessages;
    }

    public function getSavedMessages(string $level): array
    {
        return $this->savedMessages[$level] ?? [];
    }

    protected function saveMessage($level, $message): void
    {
        if (!$this->saveMessages) {
            return;
        }
        $this->savedMessages[$level][] = $message;
    }
}
