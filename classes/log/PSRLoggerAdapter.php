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

    public function __construct(PrestaShopLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function emergency($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function alert($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function critical($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function error($message, array $context = []): void
    {
        $this->logger->logError($message);
    }

    public function warning($message, array $context = []): void
    {
        $this->logger->logWarning($message);
    }

    public function notice($message, array $context = []): void
    {
        $this->logger->logInfo($message);
    }

    public function info($message, array $context = []): void
    {
        $this->logger->logInfo($message);
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
    }
}
