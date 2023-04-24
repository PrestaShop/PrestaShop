<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator\CartRule;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CartRule\ActionValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CartRule\Action;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ActionValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @dataProvider getValidData
     *
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function testValidDataDoesNotViolateTheConstraint(array $data): void
    {
        $this->validator->validate($data, new Action());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    /**
     * @dataProvider getUnsupportedConstraints
     *
     * @param Constraint $constraint
     *
     * @return void
     */
    public function testItThrowsExceptionWhenUnsupportedConstraintIsProvided(Constraint $constraint): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(['whatever, should still fail fast on constraint first'], $constraint);
    }

    /**
     * @dataProvider getInvalidValueForTypeCheck
     *
     * @param $value
     *
     * @return void
     */
    public function testItThrowsExceptionWhenInvalidValueTypeIsProvided($value): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate($value, new Action());
    }

    /**
     * @dataProvider getDataViolatingTheConstraint
     *
     * @return void
     */
    public function testItBuildsViolation($data): void
    {
        $constraint = new Action();
        $this->validator->validate($data, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public function getDataViolatingTheConstraint(): iterable
    {
        yield [[]];
        yield [['free_shipping' => false]];
        yield [['free_shipping' => '0']];
        yield [['free_shipping' => 0]];
        yield [['free_shipping' => null]];
        yield [
            [
                'disabling_switch_discount' => false,
                'discount' => [
                    'reduction' => [
                        'value' => 0,
                    ],
                ],
            ],
        ];
        yield [
            [
                'discount' => [
                    'reduction' => [],
                ],
            ],
        ];
        yield [
            [
                'disabling_switch_discount' => true,
                'discount' => [
                    'reduction' => [],
                ],
            ],
        ];
    }

    public function getValidData(): iterable
    {
        yield [
            [
                'free_shipping' => true,
            ],
        ];

        yield [
            [
                'free_shipping' => false,
                'disabling_switch_discount' => true,
                'discount' => [
                    'reduction' => [
                        'value' => '15.5',
                    ],
                ],
            ],
        ];

        yield [
            [
                'free_shipping' => false,
                'disabling_switch_discount' => true,
                'discount' => [
                    'reduction' => [
                        'value' => '27.5',
                        'type' => Reduction::TYPE_PERCENTAGE,
                    ],
                ],
            ],
        ];

        yield [
            [
                'free_shipping' => true,
                'disabling_switch_discount' => true,
                'discount' => [
                    'reduction' => [
                        'value' => '27.5',
                        'type' => Reduction::TYPE_AMOUNT,
                    ],
                ],
            ],
        ];
    }

    public function getUnsupportedConstraints(): iterable
    {
        yield [new DefaultLanguage()];
        yield [new Length(['max' => 1])];
        yield [new TypedRegex(TypedRegex::TYPE_CATALOG_NAME)];
        // there are only one constraint that is supported, no point listing all of not supported ones here
    }

    public function getInvalidValueForTypeCheck(): iterable
    {
        // only array is supported
        yield ['a'];
        yield [1];
        yield [0.5];
        yield [false];
    }

    protected function createValidator(): ActionValidator
    {
        return new ActionValidator();
    }
}
