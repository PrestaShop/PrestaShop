<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Carrier\ValueObject;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;

class CarrierReferenceIdTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $carrierReferenceId = new CarrierReferenceId(500);
        Assert::assertSame(500, $carrierReferenceId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $invalidValue): void
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_REFERENCE_ID);

        new CarrierReferenceId($invalidValue);
    }

    /**
     * @return iterable
     */
    public function getInvalidValues(): iterable
    {
        yield [0];
        yield [-5];
    }
}
