<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ValueObject;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Dimension;

class DimensionTest extends TestCase
{
    /**
     * @dataProvider getDataForTestingItIsConstructedCorrectly
     *
     * @param string $rawValue
     */
    public function testItIsConstructedCorrectly(string $rawValue): void
    {
        $dimension = new Dimension($rawValue);

        Assert::assertTrue($dimension->getDecimalValue()->equals(new DecimalNumber($rawValue)));
    }

    public function testItThrowsDomainConstraintExceptionWhenLowerThanZeroValueIsProvided(): void
    {
        $this->expectException(DomainConstraintException::class);
        new Dimension('-1');
    }

    /**
     * @return iterable
     */
    public function getDataForTestingItIsConstructedCorrectly(): iterable
    {
        yield ['0'];
        yield ['1.5'];
        yield ['50'];
        yield ['5001.999023'];
    }
}
