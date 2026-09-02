<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\SqlManagement\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\SaveSqlRequestSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler\SaveSqlRequestSettingsHandler;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

class SaveSqlRequestSettingsHandlerTest extends TestCase
{
    /**
     * @dataProvider getSettings
     */
    public function testItSavesSettingsInCorrectFormat(string $configuredValue, string $separator, int $expectedValueFormat)
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->set('PS_ENCODING_FILE_MANAGER_SQL', $configuredValue);
        $configuration->set('PS_SEPARATOR_FILE_MANAGER_SQL', $separator);

        $handler = new SaveSqlRequestSettingsHandler($configuration);
        $this->assertNull($handler->handle(new SaveSqlRequestSettingsCommand($configuredValue, $separator)));
    }

    public function getSettings()
    {
        return [
            [
                CharsetEncoding::UTF_8,
                ';',
                1,
            ],
            [
                CharsetEncoding::ISO_8859_1,
                ';',
                2,
            ],
        ];
    }
}
