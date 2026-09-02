<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\SqlManager\Exporter;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use PrestaShop\PrestaShop\Core\Export\Data\ExportableData;
use PrestaShop\PrestaShop\Core\Export\FileWriter\FileWriterInterface;
use SplFileInfo;

/**
 * Class SqlRequestExporter exports SqlRequest query execution result into CSV file under export directory.
 */
final class SqlRequestExporter implements SqlRequestExporterInterface
{
    private FileWriterInterface $csvFileWriter;
    private ConfigurationInterface $configuration;

    /**
     * @param FileWriterInterface $csvFileWriter
     * @param ConfigurationInterface $configuration
     */
    public function __construct(FileWriterInterface $csvFileWriter, ConfigurationInterface $configuration)
    {
        $this->csvFileWriter = $csvFileWriter;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function exportToFile(SqlRequestId $sqlRequestId, SqlRequestExecutionResult $result): SplFileInfo
    {
        $exportData = new ExportableData(
            $result->getColumns(),
            $result->getRows()
        );

        $exportFileName = sprintf('request_sql_%s.csv', $sqlRequestId->getValue());

        return $this->csvFileWriter->write(
            $exportFileName,
            $exportData,
            $this->configuration->get(SqlRequestSettings::FILE_SEPARATOR)
        );
    }
}
