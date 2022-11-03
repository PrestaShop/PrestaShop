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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter;

use Composer\CaBundle\CaBundle;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Tools;
use Tools as LegacyTools;

class ToolsTest extends TestCase
{
    public function provideTestCasesForBcAdd(): iterable
    {
        yield ['1.234', '5', 4, '6.2340'];
        yield ['5', '1.234', 4, '6.2340'];
        yield ['10', '0.0000000', 6, '10.000000'];
        yield ['0.0000000', '10', 6, '10.000000'];
        yield ['0.0', '0.00000002', 2, '0.00'];
    }

    /**
     * Test for refreshCaCertFile : delete de Ca Cert file, refresh and test if the file is created
     */
    public function testRefreshCaCertFile(): void
    {
        @unlink(_PS_CACHE_CA_CERT_FILE_);
        (new Tools())->refreshCaCertFile();

        // get original cacert.pem content and check it against cached version: _PS_CACHE_CA_CERT_FILE_
        $stream_context = @stream_context_create(
            [
                'http' => ['timeout' => 3],
                'ssl' => [
                    'cafile' => CaBundle::getBundledCaBundlePath(),
                ],
            ]
        );
        $original = @file_get_contents(LegacyTools::CACERT_LOCATION, false, $stream_context);
        if (empty($original)) {
            $original = @file_get_contents(CaBundle::getBundledCaBundlePath());
        }
        self::assertEquals($original, file_get_contents(_PS_CACHE_CA_CERT_FILE_));
    }
}
