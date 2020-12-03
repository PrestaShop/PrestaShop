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

namespace LegacyTests\Unit\Core\Domain\SqlManagement\CommandHandler;

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
    public function testItSavesSettingsInCorrectFormat($configuredValue, $expectedValueFormat)
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('set')
            ->with('PS_ENCODING_FILE_MANAGER_SQL', $expectedValueFormat);

        $handler = new SaveSqlRequestSettingsHandler($configuration);
        $this->assertNull($handler->handle(new SaveSqlRequestSettingsCommand($configuredValue)));
    }

    public function getSettings()
    {
        return [
            [
                CharsetEncoding::UTF_8,
                1,
            ],
            [
                CharsetEncoding::ISO_8859_1,
                2,
            ],
        ];
    }
}
