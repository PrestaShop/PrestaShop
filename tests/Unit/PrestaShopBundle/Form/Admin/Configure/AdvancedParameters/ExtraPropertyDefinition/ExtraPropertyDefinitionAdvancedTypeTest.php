<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition\ExtraPropertyApiPlacementRowType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition\ExtraPropertyDefinitionAdvancedType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition\ExtraPropertyFormPlacementRowType;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition\ExtraPropertyGridPlacementRowType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The Placement card's contract since the raw edition was removed: the three association
 * collections ARE the mapped, submitted form data (rows in provider shape, serialized back by the
 * data handler). Abandoned rows are dropped at submit; each row validates its own grammar and the
 * collections enforce the cross-row uniqueness rule.
 */
class ExtraPropertyDefinitionAdvancedTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension([
                new ExtraPropertyDefinitionAdvancedType(
                    $translator,
                    [],
                    $this->createMock(FormCatalog::class),
                    $this->createMock(GridCatalog::class),
                    $this->createMock(ApiEndpointCatalog::class),
                    new ExtraPropertyFormTypeMap(),
                ),
                new ExtraPropertyFormPlacementRowType($translator, []),
                new ExtraPropertyGridPlacementRowType($translator, []),
                new ExtraPropertyApiPlacementRowType($translator, []),
            ], []),
            // Declares the "constraints" option and runs the row Callbacks.
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testProviderRowsRoundTripThroughSubmit(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, [
            'form_type' => null,
            'form_options' => null,
            'associated_forms' => [
                ['form_id' => 'product', 'path' => 'options.suppliers', 'mode' => 'before'],
            ],
            'associated_grids' => [],
            'associated_apis' => [],
        ]);

        $form->submit([
            'form_type' => '',
            'form_options' => '',
            'associated_forms' => [
                ['form_id' => 'category', 'path' => 'parent', 'mode' => 'after'],
            ],
            'associated_grids' => [
                ['grid_id' => 'product', 'column_id' => 'reference', 'mode' => ''],
            ],
            'associated_apis' => [
                ['uri' => '/products/{productId}', 'methods' => 'GET,PATCH'],
            ],
        ]);

        $this->assertTrue($form->isValid());
        $data = $form->getData();
        $this->assertSame([['form_id' => 'category', 'path' => 'parent', 'mode' => 'after']], $data['associated_forms']);
        $this->assertSame([['grid_id' => 'product', 'column_id' => 'reference', 'mode' => '']], $data['associated_grids']);
        $this->assertSame([['uri' => '/products/{productId}', 'methods' => 'GET,PATCH']], $data['associated_apis']);
    }

    public function testAbandonedRowsAreDroppedAtSubmit(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, $this->emptyCardData());

        $form->submit(array_merge($this->emptyCardSubmission(), [
            'associated_forms' => [
                ['form_id' => 'product', 'path' => '', 'mode' => ''],
                ['form_id' => '', 'path' => '', 'mode' => ''],
            ],
        ]));

        $this->assertTrue($form->isValid());
        $this->assertSame(
            [['form_id' => 'product', 'path' => null, 'mode' => '']],
            $form->getData()['associated_forms']
        );
    }

    public function testRowWithContentButNoIdGetsARowLocatedError(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, $this->emptyCardData());

        $form->submit(array_merge($this->emptyCardSubmission(), [
            'associated_forms' => [
                ['form_id' => '', 'path' => 'options', 'mode' => ''],
            ],
        ]));

        $this->assertFalse($form->isValid());
        $errors = iterator_to_array($form->get('associated_forms')->getErrors(true), false);
        $this->assertCount(1, $errors);
        $this->assertSame('The form identifier is required.', $errors[0]->getMessage());
    }

    public function testInvalidApiMethodSurfacesOnTheRow(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, $this->emptyCardData());

        $form->submit(array_merge($this->emptyCardSubmission(), [
            'associated_apis' => [
                ['uri' => '/products', 'methods' => 'FLY'],
            ],
        ]));

        $this->assertFalse($form->isValid());
        $errors = iterator_to_array($form->get('associated_apis')->getErrors(true), false);
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('FLY', $errors[0]->getMessage());
    }

    public function testDuplicateFormIdsAreRejected(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, $this->emptyCardData());

        $form->submit(array_merge($this->emptyCardSubmission(), [
            'associated_forms' => [
                ['form_id' => 'product', 'path' => '', 'mode' => ''],
                ['form_id' => 'product', 'path' => 'options', 'mode' => ''],
            ],
        ]));

        $this->assertFalse($form->isValid());
        $errors = iterator_to_array($form->get('associated_forms')->getErrors(true), false);
        $this->assertCount(1, $errors);
        $this->assertSame('Duplicate form "product" — each form may only be referenced once.', $errors[0]->getMessage());
    }

    public function testTamperedModeBlocksTheRowNotTheCard(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, $this->emptyCardData());

        $form->submit(array_merge($this->emptyCardSubmission(), [
            'associated_forms' => [
                ['form_id' => 'product', 'path' => 'options', 'mode' => 'sideways'],
            ],
        ]));

        $this->assertFalse($form->isValid());
        $this->assertFalse($form->get('associated_forms')->get('0')->get('mode')->isSynchronized());
    }

    public function testEmptyDataGivesEmptyCollections(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, null);

        $this->assertSame([], (array) $form->get('associated_forms')->getData());
        $this->assertSame([], (array) $form->get('associated_grids')->getData());
        $this->assertSame([], (array) $form->get('associated_apis')->getData());
    }

    public function testCatalogsAreExposedAsAViewVariable(): void
    {
        $form = $this->factory->create(ExtraPropertyDefinitionAdvancedType::class, null);
        $view = $form->createView();

        $this->assertArrayHasKey('extra_property_catalogs', $view->vars);
        $this->assertSame(
            ['forms', 'grids', 'apis', 'defaultFormTypes'],
            array_keys($view->vars['extra_property_catalogs'])
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyCardData(): array
    {
        return [
            'form_type' => null,
            'form_options' => null,
            'associated_forms' => [],
            'associated_grids' => [],
            'associated_apis' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyCardSubmission(): array
    {
        return [
            'form_type' => '',
            'form_options' => '',
            'associated_forms' => [],
            'associated_grids' => [],
            'associated_apis' => [],
        ];
    }
}
