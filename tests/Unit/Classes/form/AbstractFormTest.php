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
     * @dataProvider provideSubmittedValues
     */
    public function testFillWithNormalizesTextFields(string $type, $value, $expected): void
    {
        $form = $this->getFormWithFormatter(['field' => (new FormField())->setName('field')->setType($type)]);

        $form->fillWith(['field' => $value]);

        self::assertSame($expected, $form->getValue('field'));
        // A subsequent partial submission keeps the normalized value.
        $form->fillWith([]);
        self::assertSame($expected, $form->getValue('field'));
    }

    public static function provideSubmittedValues(): iterable
    {
        yield 'name' => ['text', "\t Anne Marie \r\n", 'Anne Marie'];
        yield 'postcode with internal space' => ['text', ' SW1A 1AA ', 'SW1A 1AA'];
        yield 'email' => ['email', ' customer@example.com ', 'customer@example.com'];
        yield 'phone' => ['tel', ' +48 123 456 789 ', '+48 123 456 789'];
        yield 'url' => ['url', ' https://example.com/ ', 'https://example.com/'];
        yield 'search' => ['search', ' blue shirt ', 'blue shirt'];
        yield 'multiline text' => ['textarea', "\n First line\nSecond line \n", "First line\nSecond line"];
        yield 'blank text' => ['text', " \t\n", ''];
        yield 'password' => ['password', ' secret ', ' secret '];
        yield 'hidden token' => ['hidden', ' token ', ' token '];
        yield 'select identifier' => ['select', ' choice ', ' choice '];
        yield 'custom field type' => ['custom', ' value ', ' value '];
        yield 'null' => ['text', null, null];
        yield 'integer' => ['text', 0, 0];
        yield 'boolean' => ['checkbox', true, true];
        yield 'array' => ['text', [' value '], [' value ']];
    }

    public function testWhitespaceDoesNotSatisfyARequiredTextField(): void
    {
        $form = $this->getFormWithFormatter(['field' => (new FormField())->setName('field')->setRequired(true)]);

        $form->fillWith(['field' => " \t\n"]);

        self::assertFalse($form->validate());
        self::assertContains('Required field', $form->getField('field')->getErrors());
    }

    private function getFormWithFormatter(array $fields): AbstractForm
    {
        $formatter = $this->createMock(FormFormatterInterface::class);
        $formatter->method('getFormat')->willReturn($fields);
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return $this->getMockForAbstractClass(AbstractForm::class, [
            $this->createMock(Smarty::class),
            $translator,
            $formatter,
        ]);
    }

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
