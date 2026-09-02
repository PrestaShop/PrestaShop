<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\SaveSqlRequestSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

/**
 * Class SaveSqlRequestSettingsHandler handles command to save SqlRequest settings.
 */
#[AsCommandHandler]
final class SaveSqlRequestSettingsHandler implements SaveSqlRequestSettingsHandlerInterface
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
    public function handle(SaveSqlRequestSettingsCommand $command)
    {
        $this->configuration->set(SqlRequestSettings::FILE_ENCODING, $this->getEncodingFileValue($command));
        $this->configuration->set(SqlRequestSettings::FILE_SEPARATOR, $command->getFileSeparator());
    }

    /**
     * File encodings are saved as integer values in databases.
     *
     * @param SaveSqlRequestSettingsCommand $command
     *
     * @return int
     */
    private function getEncodingFileValue(SaveSqlRequestSettingsCommand $command): int
    {
        $valuesMapping = [
            CharsetEncoding::UTF_8 => 1,
            CharsetEncoding::ISO_8859_1 => 2,
        ];

        return $valuesMapping[$command->getFileEncoding()];
    }
}
