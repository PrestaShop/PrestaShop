<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\OptionsProvider;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\CombinationFormOptionsProvider;

class CombinationFormOptionsProviderTest extends TestCase
{
    public function testGetDefaultOptions(): void
    {
        $provider = new CombinationFormOptionsProvider();
        $defaultOptions = $provider->getDefaultOptions([]);
        $this->assertEquals([], $defaultOptions);
    }

    /**
     * @dataProvider getTestData
     */
    public function testGetOptions(array $formData, array $expectedOptions): void
    {
        $provider = new CombinationFormOptionsProvider();
        $options = $provider->getOptions(51, $formData);
        $this->assertEquals($expectedOptions, $options);
    }

    public function getTestData(): Generator
    {
        yield [[], ['product_id' => null]];
        yield [['product_id' => null], ['product_id' => null]];
        yield [['product_id' => 42], ['product_id' => 42]];
    }
}
