<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Title\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;

class GenderTest extends TestCase
{
    public function testItThrowsExceptionWhenTypeIsNotGood(): void
    {
        $this->expectException(TitleConstraintException::class);
        $gender = new Gender(4);
    }

    /**
     * @dataProvider dataProviderGender
     *
     * @param int $gender
     *
     * @return void
     *
     * @throws TitleConstraintException
     */
    public function testGoodValues(int $gender): void
    {
        $genderId = new Gender($gender);
        $this->assertEquals($genderId->getValue(), $gender);
    }

    public function dataProviderGender(): array
    {
        return [
            [
                Gender::TYPE_MALE,
            ],
            [
                Gender::TYPE_FEMALE,
            ],
            [
                Gender::TYPE_OTHER,
            ],
        ];
    }
}
