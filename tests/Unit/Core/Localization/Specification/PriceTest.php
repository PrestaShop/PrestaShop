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

namespace Tests\Unit\Core\Localization\Specification;

use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

class PriceTest extends NumberTest
{
    /**
     * Let's override numberSpec with the tested Currency specification
     * All NumberTest tests are supposed to pass with a Currency spec.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->latinNumberSpec = new PriceSpecification(
            '',
            '',
            ['latin' => $this->latinSymbolList, 'arab' => $this->arabSymbolList],
            3,
            0,
            true,
            3,
            3,
            PriceSpecification::CURRENCY_DISPLAY_SYMBOL,
            '',
            ''
        );
    }
}
