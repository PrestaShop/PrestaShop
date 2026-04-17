<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\FeatureValue\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

class FeatureValueIdTest extends TestCase
{
    public function testValidInput()
    {
        $vo = new FeatureValueId(42);
        $this->assertNotNull($vo);
    }

    /**
     * @dataProvider getInvalidInput
     */
    public function testInvalidInput(int $featureValueId)
    {
        $this->expectException(InvalidFeatureValueIdException::class);
        new FeatureValueId($featureValueId);
    }

    public function getInvalidInput()
    {
        yield [
            0,
        ];

        yield [
            -1,
        ];
    }
}
