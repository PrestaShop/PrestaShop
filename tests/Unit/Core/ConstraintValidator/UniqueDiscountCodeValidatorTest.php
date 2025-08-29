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

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueDiscountCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\UniqueDiscountCodeValidator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class UniqueDiscountCodeValidatorTest
 */
class UniqueDiscountCodeValidatorTest extends ConstraintValidatorTestCase
{
    protected DiscountRepository|MockObject $discountRepository;

    public function setUp(): void
    {
        /* @var DiscountRepository|MockObject $discountRepository */
        $this->discountRepository = $this->createMock(DiscountRepository::class);
        parent::setUp();
    }

    public function testItDetectsIncorrectConstraintType()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate([], new NotBlank());
    }

    public function testItDoesNotValidateEmptyCode()
    {
        $this->mockFormWithData(['id' => 1]);
        $this->validator->validate('', new UniqueDiscountCode());
        $this->assertNoViolation();
    }

    public function testItValidatesUniqueCode()
    {
        $this->mockFormWithData(['id' => 1]);

        $this->discountRepository
            ->expects($this->once())
            ->method('getIdByCode')
            ->with('UNIQUE_CODE')
            ->willReturn(null);

        $this->validator->validate('UNIQUE_CODE', new UniqueDiscountCode());
        $this->assertNoViolation();
    }

    public function testIfViolateUnicityForAnotherCode()
    {
        $this->mockFormWithData(['id' => 1]);

        $this->discountRepository
            ->expects($this->once())
            ->method('getIdByCode')
            ->with('UNIQUE_CODE')
            ->willReturn(2);

        $constraint = new UniqueDiscountCode();
        $this->validator->validate('UNIQUE_CODE', $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('%s', '2')
            ->assertRaised();
    }

    public function testIfDoesNotViolateUnicityForSameCode()
    {
        $this->mockFormWithData(['id' => 1]);

        $this->discountRepository
            ->expects($this->once())
            ->method('getIdByCode')
            ->with('UNIQUE_CODE')
            ->willReturn(1);

        $this->validator->validate('UNIQUE_CODE', new UniqueDiscountCode());
        $this->assertNoViolation();
    }

    protected function createValidator()
    {
        return new UniqueDiscountCodeValidator($this->discountRepository);
    }

    private function mockFormWithData(array $formData): void
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('getRoot')->willReturn($form);
        $form->method('getNormData')->willReturn($formData);

        $this->setObject($form);
    }
}
