<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator;

use Generator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\PositiveOrZero;
use PrestaShop\PrestaShop\Core\ConstraintValidator\PositiveOrZeroValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PositiveOrZeroValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param string|float $value
     */
    public function testItSucceedsWhenValidValueIsGiven($value): void
    {
        $this->validator->validate($value, new PositiveOrZero());

        $this->assertNoViolation();
    }

    /**
     * @return Generator
     */
    public function getValidValues(): Generator
    {
        yield ['1'];
        yield ['10.05'];
        yield ['0.033'];
        yield ['0'];
        yield [0.3];
        yield [1.555];
        yield [1555];
        yield [0];
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param string|float|bool|array $value
     */
    public function testItFailsWhenInvalidValueIsProvided($value)
    {
        $constraint = new PositiveOrZero();
        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield ['-1'];
        yield ['-10.05'];
        yield ['-0.033'];
        yield [''];
        yield [-0.3];
        yield [-1.555];
        yield [-15550000];
        yield [true];
        yield [[]];
    }

    /**
     * @return PositiveOrZeroValidator
     */
    protected function createValidator(): PositiveOrZeroValidator
    {
        return new PositiveOrZeroValidator();
    }
}
