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

namespace Tests\Unit\PrestaShopBundle\Form\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLength;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLengthValidator;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TinyMceMaxLengthValidatorTest extends TestCase
{
    private const MAX_LENGTH = 42;

    /**
     * @dataProvider getValidationData
     *
     * @param string $content
     * @param bool $isValid
     */
    public function testValidate(string $content, bool $isValid): void
    {
        $validator = new TinyMceMaxLengthValidator($this->getTranslator());
        $context = $this->getContext($isValid);
        $validator->initialize($context);

        $constraint = new TinyMceMaxLength([
            'max' => static::MAX_LENGTH,
        ]);
        $validator->validate($content, $constraint);
    }

    public function getValidationData(): iterable
    {
        yield [
            'Valid text',
            true,
        ];

        yield [
            'Valid text too long only because of HTML',
            true,
        ];

        yield [
            '<p>Valid text too long only because of HTML</p>',
            true,
        ];

        yield [
            'Invalid text that is a too long even without HTML',
            false,
        ];

        yield [
            '<p>Invalid text that is a too long even without HTML</p>',
            false,
        ];
    }

    private function getContext(bool $isValid): ExecutionContextInterface
    {
        $context = $this->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($isValid) {
            $context
                ->expects($this->never())
                ->method('addViolation')
            ;
        } else {
            $context
                ->expects($this->once())
                ->method('addViolation')
            ;
        }

        return $context;
    }

    private function getTranslator(): TranslatorInterface
    {
        $translator = $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator
            ->expects($this->any())
            ->method('trans')
            ->willReturn('translated wording')
        ;

        return $translator;
    }
}
