<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use Symfony\Component\DependencyInjection\Container;

/**
 * @phpstan-import-type LevelName from Logger
 * @phpstan-import-type Level from Logger
 * @phpstan-import-type Record from Logger
 *
 * @phpstan-type FormattedRecord array{message: string, context: mixed[], level: Level, level_name: LevelName, channel: string, datetime: \DateTimeImmutable, extra: mixed[], formatted: mixed}
 *
 * This handler is an interface between Monolog and the legacy logger.
 *
 * It also provides a feature that allows you saving log records, it is always disabled by default, but you can temporarily
 * enable the saving of recors, which may be useful to get warning messages and then display them as flash messages in controllers.
 */
class LogHandler extends AbstractProcessingHandler
{
    protected $container;

    /**
     * @var array<int, array<int, array{level: int, message: string, context: array}>>
     */
    protected array $savedRecords = [];

    protected bool $recordsSaved = false;

    public function __construct(Container $container, $level = Logger::DEBUG, $bubble = true)
    {
        $this->container = $container;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @phpstan-param FormattedRecord $record
     */
    protected function write(array $record): void
    {
        /** @var LegacyLogger $logger */
        $logger = $this->container->get('prestashop.adapter.legacy.logger');
        $logger->log($record['level'], $record['message'], $record['context']);
        if (!empty($record['level']) && $record['level'] > Logger::DEBUG && $this->recordsSaved) {
            $this->savedRecords[$record['level']][] = $record;
        }
    }

    public function isRecordsSaved(): bool
    {
        return $this->recordsSaved;
    }

    /**
     * All messages logged after this method is called are stored in a class field.
     */
    public function startSavingRecords(): void
    {
        $this->recordsSaved = true;
    }

    /**
     * Stop saving log records and clear the saved records.
     */
    public function stopSavingRecords(): void
    {
        $this->recordsSaved = false;
        $this->savedRecords = [];
    }

    public function getSavedRecords(int $level): array
    {
        return $this->savedRecords[$level] ?? [];
    }

    public function getAllSavedRecords(): array
    {
        return $this->savedRecords;
    }
}
