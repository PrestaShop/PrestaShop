<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCatalog;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition\ExtraPropertyConstraintRowType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition\ExtraPropertyDefinitionValidationType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The Validation card's contract since the raw edition was removed: the constraint rows ARE the
 * mapped, submitted form data (provider shape in, folded back to the DSL by the data handler).
 * Abandoned rows are dropped at submit; each row validates its own token through the mapper.
 */
class ExtraPropertyDefinitionValidationTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension([
                new ExtraPropertyDefinitionValidationType($translator, [], new ExtraPropertyConstraintCatalog()),
                new ExtraPropertyConstraintRowType($translator, []),
            ], []),
            // Runs the row Callbacks.
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testProviderRowsRoundTripThroughSubmit(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, [
            'constraints' => [
                ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
            ],
        ]);

        $form->submit([
            'constraints' => [
                ['name' => 'DefaultLanguage', 'options' => "'Video link'", 'per_language' => '0'],
                ['name' => 'Url', 'options' => '', 'per_language' => '1'],
                ['name' => 'Length', 'options' => 'max: 255', 'per_language' => '1'],
            ],
        ]);

        $this->assertTrue($form->isValid());
        // TextType/HiddenType normalize empty submitted values to null on rebind.
        $this->assertSame([
            ['name' => 'DefaultLanguage', 'options' => "'Video link'", 'per_language' => '0'],
            ['name' => 'Url', 'options' => null, 'per_language' => '1'],
            ['name' => 'Length', 'options' => 'max: 255', 'per_language' => '1'],
        ], $form->getData()['constraints']);
    }

    public function testAbandonedRowsAreDroppedAtSubmit(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, ['constraints' => []]);

        $form->submit([
            'constraints' => [
                ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
                // Zone bookkeeping alone (per_language) does not make a row worth keeping.
                ['name' => '', 'options' => '', 'per_language' => '1'],
            ],
        ]);

        $this->assertTrue($form->isValid());
        $this->assertSame(
            [['name' => 'NotBlank', 'options' => null, 'per_language' => '0']],
            $form->getData()['constraints']
        );
    }

    public function testUnknownConstraintNameSurfacesOnTheRow(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, ['constraints' => []]);

        $form->submit([
            'constraints' => [
                ['name' => 'Lenght', 'options' => 'max: 64', 'per_language' => '0'],
            ],
        ]);

        $this->assertFalse($form->isValid());
        $errors = iterator_to_array($form->get('constraints')->getErrors(true), false);
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Lenght', $errors[0]->getMessage());
        $this->assertStringNotContainsString('Line 1:', $errors[0]->getMessage());
    }

    public function testInvalidOptionSurfacesOnTheRow(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, ['constraints' => []]);

        $form->submit([
            'constraints' => [
                // NotBlank takes no value: the mapper rejects the positional argument.
                ['name' => 'NotBlank', 'options' => '5', 'per_language' => '0'],
            ],
        ]);

        $this->assertFalse($form->isValid());
        $errors = iterator_to_array($form->get('constraints')->getErrors(true), false);
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('does not accept a value', $errors[0]->getMessage());
    }

    public function testOptionsWithoutANameGetADedicatedError(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, ['constraints' => []]);

        $form->submit([
            'constraints' => [
                ['name' => '', 'options' => 'max: 64', 'per_language' => '0'],
            ],
        ]);

        $this->assertFalse($form->isValid());
        $errors = iterator_to_array($form->get('constraints')->getErrors(true), false);
        $this->assertCount(1, $errors);
        $this->assertSame('The constraint name is required.', $errors[0]->getMessage());
    }

    public function testEmptyDataGivesAnEmptyCollection(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, null);

        $this->assertSame([], (array) $form->get('constraints')->getData());
    }

    public function testConstraintCatalogIsExposedAsAViewVariable(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionValidationType::class, null);
        $view = $form->createView();

        $this->assertArrayHasKey('extra_property_constraint_catalog', $view->vars);
        $this->assertArrayHasKey('NotBlank', $view->vars['extra_property_constraint_catalog']);
    }
}
