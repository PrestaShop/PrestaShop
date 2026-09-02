<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\Specification;

use PrestaShop\PrestaShop\Core\Localization\Specification\Percentage as PercentageSpecification;

class PercentageTest extends NumberTest
{
    /**
     * Let's override numberSpec with the tested Percentage specification
     * All NumberTest tests are supposed to pass with a Percentage spec.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->latinNumberSpec = new PercentageSpecification(
            '',
            '',
            ['latin' => $this->latinSymbolList, 'arab' => $this->arabSymbolList],
            3,
            0,
            true,
            3,
            3
        );
    }
}
