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

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\DefaultLanguageValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class DefaultLanguageValidatorTest
 */
class DefaultLanguageValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var int
     */
    private $defaultLanguageId;

    public function setUp(): void
    {
        $this->defaultLanguageId = 1;

        parent::setUp();
    }

    public function testItDetectsIncorrectConstraintType()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate([], new NotBlank());
    }

    /**
     * @dataProvider getIncorrectTypes
     */
    public function testItDetectsIncorrectValueType($incorrectType)
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate($incorrectType, new DefaultLanguage());
    }

    public function testItFindsDefaultLanguage()
    {
        $this->validator->validate(
            [
                $this->defaultLanguageId => 'some kind of value',
            ],
            new DefaultLanguage()
        );

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getIncorrectValues
     */
    public function testItRaisesViolationWhenDefaultLanguageIsNotPreserved($valueWithMissingDefaultLanguage)
    {
        $this->validator->validate($valueWithMissingDefaultLanguage, new DefaultLanguage());

        $this->buildViolation((new DefaultLanguage())->message)
            ->setParameter('%field_name%', '')
            ->assertRaised()
        ;
    }

    public function getIncorrectTypes()
    {
        return [
            [
                '',
            ],
            [
                false,
            ],
            [
                null,
            ],
        ];
    }

    public function getIncorrectValues()
    {
        return [
            [
                [
                    0 => 'test1',
                    2 => 'test1',
                ],
            ],
            [
                [
                    0 => 'test2',
                    $this->defaultLanguageId => null,
                ],
            ],
            [
                [
                    0 => 'test3',
                    $this->defaultLanguageId => '',
                ],
            ],
        ];
    }

    protected function createValidator()
    {
        return new DefaultLanguageValidator($this->defaultLanguageId);
    }
}
