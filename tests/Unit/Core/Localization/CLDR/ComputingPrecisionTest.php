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

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;

class ComputingPrecisionTest extends TestCase
{
    /**
     * @var ComputingPrecision
     */
    private $computingPrecision;

    /**
     * Setup tested dependency
     */
    public function setUp(): void
    {
        $this->computingPrecision = new ComputingPrecision();
    }

    /**
     * @dataProvider provider
     */
    public function testGetPrecision($input, $expected)
    {
        $result = $this->computingPrecision->getPrecision($input);
        $this->assertEquals($expected, $result);
    }

    public function provider()
    {
        return [
            [1, 1],
            [3, 3],
            [0, 0],
        ];
    }
}
