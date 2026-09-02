<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

/**
 * Class GetSqlRequestSettingsHandler handles query to get SqlRequest settings.
 */
#[AsQueryHandler]
final class GetSqlRequestSettingsHandler implements GetSqlRequestSettingsHandlerInterface
{
    private ConfigurationInterface $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetSqlRequestSettings $query): SqlRequestSettings
    {
        $fileEncodingIntValue = $this->configuration->get(SqlRequestSettings::FILE_ENCODING);
        $fileSeparatorValue = $this->configuration->get(SqlRequestSettings::FILE_SEPARATOR);

        return new SqlRequestSettings(
            $this->getFileEncoding($fileEncodingIntValue),
            $fileSeparatorValue ?? ';'
        );
    }

    /**
     * File encodings are saved as integer values in databases.
     *
     * @param int|null $rawValue
     *
     * @return string
     */
    private function getFileEncoding(?int $rawValue): string
    {
        $valuesMapping = [
            1 => CharsetEncoding::UTF_8,
            2 => CharsetEncoding::ISO_8859_1,
        ];

        if (isset($valuesMapping[$rawValue])) {
            return $valuesMapping[$rawValue];
        }

        return CharsetEncoding::UTF_8;
    }
}
