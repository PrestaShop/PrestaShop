<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\FeatureValue\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

class FeatureIdTest extends TestCase
{
    /**
     * @dataProvider getValidInput
     */
    public function testValidInput(int $featureValueId): void
    {
        $vo = new FeatureId($featureValueId);
        $this->assertEquals($featureValueId, $vo->getValue());
    }

    public function getValidInput(): iterable
    {
        yield [1000];
        yield [1];
    }

    /**
     * @dataProvider getInvalidInput
     */
    public function testInvalidInput($featureValueId): void
    {
        $this->expectException(FeatureConstraintException::class);
        $this->expectExceptionCode(FeatureConstraintException::INVALID_ID);
        new FeatureId($featureValueId);
    }

    public function getInvalidInput(): iterable
    {
        yield [0];
        yield [-1];
    }
}
