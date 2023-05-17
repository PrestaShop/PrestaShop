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

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CartRuleValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CartRule;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CartRuleValidatorTest extends ConstraintValidatorTestCase
{
    protected function createContext(): ExecutionContextInterface
    {
        $context = parent::createContext();
        // override property path, or else it will fail assertions containing custom property path by always prepending string "property.path"
        $context->setNode($this->value, $this->object, $this->metadata, '');

        return $context;
    }

    /**
     * @dataProvider getValidData
     *
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function testValidDataDoesNotViolateTheConstraint(array $data): void
    {
        $this->validator->validate($data, new CartRule());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    /**
     * @dataProvider getDataViolatingTheConstraint
     *
     * @param $data
     * @param string $expectedViolation
     * @param string $expectedErrorPath
     *
     * @return void
     */
    public function testItBuildsViolation($data, string $expectedViolation, string $expectedErrorPath): void
    {
        $constraint = new CartRule();
        $this->validator->validate($data, $constraint);

        $violation = $this->buildViolation($expectedViolation)
            ->atPath($expectedErrorPath);

        $violation->assertRaised();
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
        $this->validator->validate($value, new CartRule());
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

    public function getValidData(): iterable
    {
        yield [
            [
                'actions' => [
                    'free_shipping' => true,
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'gift_product' => [
                        ['product_id' => 1],
                    ],
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'gift_product' => [
                        [
                            'product_id' => 1,
                            'combination_id' => 2,
                        ],
                    ],
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'free_shipping' => true,
                    'discount' => [
                        'disabling_switch_discount' => true,
                        'discount_application' => DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
                        'reduction' => [
                            'type' => Reduction::TYPE_AMOUNT,
                            'value' => 15,
                        ],
                    ],
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'free_shipping' => true,
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'discount_application' => DiscountApplicationType::CHEAPEST_PRODUCT,
                        'reduction' => [
                            'type' => Reduction::TYPE_PERCENTAGE,
                            'value' => 15,
                        ],
                    ],
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'free_shipping' => false,
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'discount_application' => DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
                        'reduction' => [
                            'type' => Reduction::TYPE_PERCENTAGE,
                            'value' => '15.5',
                        ],
                    ],
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'free_shipping' => false,
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'discount_application' => DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
                        'reduction' => [
                            'value' => '27.5',
                            'type' => Reduction::TYPE_PERCENTAGE,
                        ],
                    ],
                ],
                'conditions' => [
                    'product_restrictions' => [1],
                ],
            ],
        ];

        yield [
            [
                'actions' => [
                    'free_shipping' => false,
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'discount_application' => DiscountApplicationType::SPECIFIC_PRODUCT,
                        'reduction' => [
                            'value' => '27.5',
                            'type' => Reduction::TYPE_PERCENTAGE,
                        ],
                        'specific_product' => [
                            ['id' => 1],
                        ],
                    ],
                ],
                'conditions' => [
                    'product_restrictions' => [1],
                ],
            ],
        ];
    }

    public function getDataViolatingTheConstraint(): iterable
    {
        $constraint = new CartRule();

        yield 'free shipping false and no other actions present' => [
            'actions' => [
                'free_shipping' => false,
            ],
            $constraint->missingActionsMessage,
            '[actions]',
        ];

        yield 'free shipping false and gift product is empty, and no other actions present' => [
            'actions' => [
                'free_shipping' => false,
                'gift_product' => [],
            ],
            $constraint->missingActionsMessage,
            '[actions]',
        ];

        yield 'empty data' => [
            [],
            $constraint->missingActionsMessage,
            '[actions]',
        ];

        yield 'discount switch is disabled' => [
            [
                'actions' => [
                    'disabling_switch_discount' => false,
                    'discount' => [
                        'reduction' => [
                            'value' => 15,
                        ],
                        'discount_application' => DiscountApplicationType::ORDER_WITHOUT_SHIPPING,
                    ],
                ],
            ],
            $constraint->missingActionsMessage,
            '[actions]',
        ];
        yield 'missing specific product' => [
            [
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 14,
                        ],
                        'discount_application' => DiscountApplicationType::SPECIFIC_PRODUCT,
                    ],
                ],
            ],
            $constraint->missingSpecificProductMessage,
            '[actions][discount][specific_product]',
        ];

        yield 'empty specific product data' => [
            [
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 1555,
                        ],
                        'discount_application' => DiscountApplicationType::SPECIFIC_PRODUCT,
                        'specific_product' => [],
                    ],
                ],
            ],
            $constraint->missingSpecificProductMessage,
            '[actions][discount][specific_product]',
        ];

        yield 'specific product id is 0' => [
            [
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 1555,
                        ],
                        'discount_application' => DiscountApplicationType::SPECIFIC_PRODUCT,
                        'specific_product' => 0,
                    ],
                ],
            ],
            $constraint->missingSpecificProductMessage,
            '[actions][discount][specific_product]',
        ];

        yield 'specific product id is null' => [
            [
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 1555,
                        ],
                        'discount_application' => DiscountApplicationType::SPECIFIC_PRODUCT,
                        'specific_product' => null,
                    ],
                ],
            ],
            $constraint->missingSpecificProductMessage,
            '[actions][discount][specific_product]',
        ];

        yield 'invalid array structure provided for specific product' => [
            [
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 1555,
                        ],
                        'discount_application' => DiscountApplicationType::SPECIFIC_PRODUCT,
                        'specific_product' => [
                            ['product_id' => 10],
                        ],
                    ],
                ],
            ],
            $constraint->missingSpecificProductMessage,
            '[actions][discount][specific_product]',
        ];

        yield 'missing product restrictions when type is SELECTED_PRODUCTS' => [
            [
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 1555,
                        ],
                        'discount_application' => DiscountApplicationType::SELECTED_PRODUCTS,
                    ],
                ],
            ],
            $constraint->missingProductRestrictionsMessage,
            '[actions][discount][discount_application]',
        ];

        yield 'product restrictions empty when type is SELECTED PRODUCTS' => [
            [
                'conditions' => [
                    'product_restrictions' => [],
                ],
                'actions' => [
                    'disabling_switch_discount' => true,
                    'discount' => [
                        'reduction' => [
                            'value' => 1555,
                        ],
                        'discount_application' => DiscountApplicationType::SELECTED_PRODUCTS,
                    ],
                ],
            ],
            $constraint->missingProductRestrictionsMessage,
            '[actions][discount][discount_application]',
        ];
    }

    protected function createValidator(): CartRuleValidator
    {
        return new CartRuleValidator();
    }
}
