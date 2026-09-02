<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
