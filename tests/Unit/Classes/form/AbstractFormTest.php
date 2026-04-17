<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
