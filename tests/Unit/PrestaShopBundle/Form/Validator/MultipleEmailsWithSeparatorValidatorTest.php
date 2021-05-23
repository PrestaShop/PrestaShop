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

namespace Tests\Unit\PrestaShopBundle\Form\Validator;

use stdClass;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use PrestaShopBundle\Form\Validator\Constraints\MultipleEmailsWithSeparator;
use PrestaShopBundle\Form\Validator\Constraints\MultipleEmailsWithSeparatorValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use InvalidArgumentException;
use Exception;


class MultipleEmailsWithSeparatorValidatorTest extends ConstraintValidatorTestCase{

    public const TEST_EMAILS_SEPARATOR = '|';
    
    protected function createValidator()
    {
        return new MultipleEmailsWithSeparatorValidator();
    }
    
    /**
     * @param string $separator
     * @param string|null $message
     * 
     * @return MultipleEmailsWithSeparator
     */
    private function getConstraintInstance(string $separator = self::TEST_EMAILS_SEPARATOR, ?string $message = null): MultipleEmailsWithSeparator
    {
        return new MultipleEmailsWithSeparator([
            'separator' => $separator,
            'message' => $message,
        ]);
    }
    
    /**
     * @param mixed $multipleEmailsWithSeparator
     * 
     * @dataProvider exceptionsInvalidMultipleEmailsWithSeparatorProvider
     */
    public function testExceptionsInvalidMultipleEmailsWithSeparator(Exception $expectedException, $multipleEmailsWithSeparator): void
    {
        $expectedExceptionClassName = \get_class($expectedException);
        $this->expectException($expectedExceptionClassName);
        $this->validator->validate(
            $multipleEmailsWithSeparator, 
            $this->getConstraintInstance()
        );
    }
    
    public function exceptionsInvalidMultipleEmailsWithSeparatorProvider(): array{
        return [
            [new InvalidArgumentException(), new stdClass()],
            [new InvalidArgumentException(), null],
            [new InvalidArgumentException(), false],
            [new InvalidArgumentException(), true],
            [new InvalidArgumentException(), 6666],

        ];
    }
    
    /**
     * @param string $multipleEmailsWithSeparator
     * @param string $separator
     * 
     * @dataProvider validMultipleEmailsWithSeparatorProvider
     */
    public function testValidMultipleEmailsWithSeparator(string $multipleEmailsWithSeparator, string $separator): void
    {
        $constraint = $this->getConstraintInstance($separator);
        
        $this->validator->validate($multipleEmailsWithSeparator, $constraint);

        $this->assertNoViolation();
    }
    
    public function validMultipleEmailsWithSeparatorProvider(): array {
        return [
            ['a@test.com', '|'],
            ['a@test.com|b@test.com', '|'],
            ['a@test.com|b@test.com|c@test.com', '|'],
            ['a@test.com', ';'],
            ['a@test.com;b@test.com', ';'],
            ['a@test.com;b@test.com;c@test.com', ';'],
            ['a@test.com', ','],
            ['a@test.com,b@test.com', ','],
            ['a@test.com,b@test.com,c@test.com', ','],
            ['a@test.com', ':'],
            ['a@test.com:b@test.com', ':'],
            ['a@test.com:b@test.com:c@test.com', ':'],
        ];
    }
    
    /**
     * @param string $code error code
     * @param string|null $multipleEmailsWithSeparator
     * @param string $separator mais separator
     * 
     * @dataProvider InvalidMultipleEmailsWithSeparatorProvider
     */
    public function testInvalidMultipleEmailsWithSeparator(string $code, ?string $multipleEmailsWithSeparator, string $separator): void
    {
        $fakeTestMessage = 'fakeMessage';
        $constraint = $this->getConstraintInstance($separator, $fakeTestMessage);

        $this->validator->validate($multipleEmailsWithSeparator, $constraint);
        
        $this->buildViolation($fakeTestMessage)
            ->setParameter('{{ value }}', '"'.$multipleEmailsWithSeparator.'"')
            ->setCode($code)
            ->assertRaised()
        ;

    }
    
    public function InvalidMultipleEmailsWithSeparatorProvider(): array {
        return [
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@,test', '|'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com|@^testcom', '|'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com|b@test.com|ctest.com', '|'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@@test.com', ';'], 
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com;@test.com', ';'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com;b@test.com;.@@test.com', ';'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'atest.com', ','],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com,b@test.', ','],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@testcom,b@ç test.com,c@test.com', ','],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a uk.@test.com', ':'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com:b@test 84.cl', ':'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@test.com:b@test.com:@test.com', ':'],
            [MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE, 'a@testcom,b@ç test.com,c@test.com', ','],
        ];
    }
    
}
