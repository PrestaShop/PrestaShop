<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\form;

use FormField;
use PHPUnit\Framework\TestCase;

class FormFieldTest extends TestCase
{
    public function testConstruct(): void
    {
        $formField = new FormField();

        self::assertEquals(null, $formField->getMinLength());
        self::assertEquals([
            'name' => '',
            'type' => 'text',
            'required' => false,
            'label' => '',
            'value' => null,
            'availableValues' => [],
            'minLength' => null,
            'maxLength' => null,
            'errors' => [],
            'autocomplete' => '',
            'attr' => [],
        ], $formField->toArray());
    }

    public function testMinLength(): void
    {
        $expectedValue = rand(0, 1000);
        $formField = new FormField();
        $formField->setMinLength($expectedValue);

        self::assertEquals($expectedValue, $formField->getMinLength());
        self::assertEquals([
            'name' => '',
            'type' => 'text',
            'required' => false,
            'label' => '',
            'value' => null,
            'availableValues' => [],
            'minLength' => $expectedValue,
            'maxLength' => null,
            'errors' => [],
            'autocomplete' => '',
            'attr' => [],
        ], $formField->toArray());

        $formField->setMinLength(null);

        self::assertEquals(null, $formField->getMinLength());
        self::assertEquals([
            'name' => '',
            'type' => 'text',
            'required' => false,
            'label' => '',
            'value' => null,
            'availableValues' => [],
            'minLength' => null,
            'maxLength' => null,
            'errors' => [],
            'autocomplete' => '',
            'attr' => [],
        ], $formField->toArray());
    }
}
