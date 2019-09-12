<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Domain\SqlManagement\QueryHandler;

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
    public function testItReturnsCorrectSettings($configuredValue, $expectedValue)
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')
            ->with('PS_ENCODING_FILE_MANAGER_SQL')
            ->willReturn($configuredValue);

        $getSqlRequestSettingsHandler = new GetSqlRequestSettingsHandler($configuration);
        $sqlRequestSettings = $getSqlRequestSettingsHandler->handle(new GetSqlRequestSettings());

        $this->assertInstanceOf(SqlRequestSettings::class, $sqlRequestSettings);
        $this->assertEquals($expectedValue, $sqlRequestSettings->getFileEncoding());
    }

    public function getInvalidConfiguration()
    {
        return [
            [
                null,
                CharsetEncoding::UTF_8,
            ],
            [
                1,
                CharsetEncoding::UTF_8,
            ],
            [
                2,
                CharsetEncoding::ISO_8859_1,
            ],
            [
                9999,
                CharsetEncoding::UTF_8,
            ],
        ];
    }
}
