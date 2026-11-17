<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Store\Validate;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Store\Validate\StoreValidator;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;

class StoreValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidCoordinates
     */
    public function testValidCoordinatesAreAccepted(?string $latitude, ?string $longitude): void
    {
        $this->expectNotToPerformAssertions();

        (new StoreValidator())->assertCoordinates(
            null !== $latitude ? new DecimalNumber($latitude) : null,
            null !== $longitude ? new DecimalNumber($longitude) : null
        );
    }

    public static function getValidCoordinates(): Generator
    {
        yield 'no coordinates' => [null, null];
        yield 'latitude only' => ['48.8566', null];
        yield 'longitude only' => [null, '2.3522'];
        yield 'zero' => ['0', '0'];
        yield 'lower bounds' => ['-90', '-180'];
        yield 'upper bounds' => ['90', '180'];
    }

    /**
     * @dataProvider getInvalidCoordinates
     */
    public function testInvalidCoordinatesAreRefused(?string $latitude, ?string $longitude, int $expectedCode): void
    {
        $this->expectException(StoreConstraintException::class);
        $this->expectExceptionCode($expectedCode);

        (new StoreValidator())->assertCoordinates(
            null !== $latitude ? new DecimalNumber($latitude) : null,
            null !== $longitude ? new DecimalNumber($longitude) : null
        );
    }

    public static function getInvalidCoordinates(): Generator
    {
        yield 'latitude just above' => ['90.00000001', '0', StoreConstraintException::INVALID_LATITUDE];
        yield 'latitude just below' => ['-90.00000001', '0', StoreConstraintException::INVALID_LATITUDE];
        yield 'latitude in the longitude range' => ['180', '0', StoreConstraintException::INVALID_LATITUDE];
        yield 'latitude too long for the column' => ['10000', null, StoreConstraintException::INVALID_LATITUDE];
        yield 'longitude just above' => ['0', '180.00000001', StoreConstraintException::INVALID_LONGITUDE];
        yield 'longitude just below' => [null, '-180.00000001', StoreConstraintException::INVALID_LONGITUDE];
        yield 'longitude too long for the column' => [null, '-10000', StoreConstraintException::INVALID_LONGITUDE];
    }
}
