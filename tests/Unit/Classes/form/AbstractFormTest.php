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

namespace Tests\Unit\Classes\form;

use AbstractForm;
use FormField;
use FormFormatterInterface;
use PHPUnit\Framework\TestCase;
use Smarty;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractFormTest extends TestCase
{
    /**
     * @dataProvider dataProviderValidateMinLength
     */
    public function testValidateMinLength(array $fields, array $values, bool $isValidated, array $errors): void
    {
        $form = $this->getMockAbstractForm($fields);
        foreach ($values as $fieldKey => $fieldValue) {
            $form->setValue($fieldKey, $fieldValue);
        }

        self::assertEquals($isValidated, $form->validate());
        self::assertEquals($errors, $form->getErrors());
    }

    /**
     * @return array[]
     */
    public function dataProviderValidateMinLength(): array
    {
        return [
            // Required field but empty
            [
                ['field' => (new FormField())->setName('field')->setRequired(true)],
                ['field' => null],
                false,
                [
                    '' => [],
                    'field' => ['Required field'],
                ],
            ],
            // Required field (min length)
            [
                ['field' => (new FormField())->setName('field')->setRequired(true)->setMinLength(5)],
                ['field' => 'abc'],
                false,
                [
                    '' => [],
                    'field' => ['The %1$s field is too short (%2$d chars min).'],
                ],
            ],
            // Required field (max length)
            [
                ['field' => (new FormField())->setName('field')->setRequired(true)->setMaxLength(5)],
                ['field' => 'abcdef'],
                false,
                [
                    '' => [],
                    'field' => ['The %1$s field is too long (%2$d chars max).'],
                ],
            ],
            // Not Required field but empty
            [
                ['field' => (new FormField())->setName('field')->setRequired(false)],
                ['field' => null],
                true,
                [
                    '' => [],
                    'field' => [],
                ],
            ],
            // Not Required field (min length)
            [
                ['field' => (new FormField())->setName('field')->setRequired(false)->setMinLength(5)],
                ['field' => 'abc'],
                false,
                [
                    '' => [],
                    'field' => ['The %1$s field is too short (%2$d chars min).'],
                ],
            ],
            // Not Required field (max length)
            [
                ['field' => (new FormField())->setName('field')->setRequired(false)->setMaxLength(5)],
                ['field' => 'abcdef'],
                false,
                [
                    '' => [],
                    'field' => ['The %1$s field is too long (%2$d chars max).'],
                ],
            ],
        ];
    }

    protected function getMockAbstractForm(array $fields): AbstractForm
    {
        $mockSmarty = $this->getMockBuilder(Smarty::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockTranslatorInterface = $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockTranslatorInterface
            ->method('trans')
            ->willReturnArgument(0);
        $mockFormFormatterInterface = $this->getMockBuilder(FormFormatterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $class = new class($mockSmarty, $mockTranslatorInterface, $mockFormFormatterInterface) extends AbstractForm {
            public function fillWith(array $params = [])
            {
                $this->formFields = $params;
            }

            public function getTemplateVariables()
            {
            }

            public function submit()
            {
            }
        };

        $class->fillWith($fields);

        return $class;
    }
}
