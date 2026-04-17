<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\SqlManagement\QueryHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetSqlRequestSettingsHandler;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

class GetSqlRequestSettingsHandlerTest extends TestCase
{
    /**
     * @dataProvider getInvalidConfiguration
     */
    public function testItReturnsCorrectSettings(?int $configuredValue, string $configuredSeparatorValue, string $expectedValue): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration
            ->method('get')
            ->willReturnOnConsecutiveCalls($configuredValue, $configuredSeparatorValue);

        $getSqlRequestSettingsHandler = new GetSqlRequestSettingsHandler($configuration);
        $sqlRequestSettings = $getSqlRequestSettingsHandler->handle(new GetSqlRequestSettings());

        $this->assertInstanceOf(SqlRequestSettings::class, $sqlRequestSettings);
        $this->assertEquals($expectedValue, $sqlRequestSettings->getFileEncoding());
    }

    public function getInvalidConfiguration(): array
    {
        return [
            [
                null,
                ';',
                CharsetEncoding::UTF_8,
            ],
            [
                1,
                ';',
                CharsetEncoding::UTF_8,
            ],
            [
                2,
                ';',
                CharsetEncoding::ISO_8859_1,
            ],
            [
                9999,
                ';',
                CharsetEncoding::UTF_8,
            ],
        ];
    }
}
