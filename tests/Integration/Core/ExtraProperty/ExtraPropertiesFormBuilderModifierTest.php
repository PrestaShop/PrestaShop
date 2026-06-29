<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormBuilderModifier;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShopBundle\Form\Admin\Type\NavigationTabType;
use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;

/**
 * Integration test for the form placement of extra properties and the symmetry between the builder
 * (where the field is added) and the persister (where the submitted value is read back).
 *
 * We use an integration test because building representative form hierarchies (NavigationTab forms,
 * nested compound sub-forms) by hand is error-prone; the real form factory builds them faithfully.
 */
class ExtraPropertiesFormBuilderModifierTest extends AbstractFormTester
{
    private const FIELD_NAME = 'extra_demoextrafield_is_dangerous';

    public function testBareFormIdOnSimpleFormAppendsAtRoot(): void
    {
        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($this->definition('product'))->apply($builder, 'product', null);

        $this->assertTrue($builder->has(self::FIELD_NAME), 'Field should be appended at root on a simple form.');
    }

    public function testDefinitionConstraintsAreAttachedToTheFieldWithoutAutoNotBlank(): void
    {
        $url = new \Symfony\Component\Validator\Constraints\Url();
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            required: true,
            associatedForms: ['product'],
            formFieldType: TextType::class,
            constraints: [$url],
            labelWording: 'Dangerous product',
        );

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition)->apply($builder, 'product', null);

        $constraints = $builder->get(self::FIELD_NAME)->getOption('constraints');

        // The definition's constraints are attached verbatim to the field …
        $this->assertSame([$url], $constraints);
        // … and required no longer injects a server-side NotBlank — requiredness is the module's job.
        foreach ($constraints as $constraint) {
            $this->assertNotInstanceOf(\Symfony\Component\Validator\Constraints\NotBlank::class, $constraint);
        }
    }

    public function testLangConstraintsAttachToOuterTranslatableType(): void
    {
        $all = new \Symfony\Component\Validator\Constraints\All([new \Symfony\Component\Validator\Constraints\Url()]);
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::LANG,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            formFieldType: TextType::class,
            constraints: [$all],
            labelWording: 'Video link',
        );

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition)->apply($builder, 'product', null);

        $field = $builder->get(self::FIELD_NAME);
        // LANG constraints validate the whole [id_lang => value] array, so they attach to the OUTER TranslatableType
        // — NOT nested per-language under the children's options (which is where per-element rules used to live).
        $this->assertSame([$all], $field->getOption('constraints'));
        $this->assertArrayNotHasKey('constraints', (array) $field->getOption('options'));
    }

    public function testBareFormIdOnNavigationTabFormCreatesExtraFieldsSection(): void
    {
        $builder = $this->createNavigationTabFormBuilder();
        $this->makeModifier($this->definition('product'))->apply($builder, 'product', null);

        $this->assertTrue($builder->has(ExtraPropertiesFormBuilderModifier::DEFAULT_FALLBACK_TAB), 'A dedicated extra_fields tab should be created.');
        $tab = $builder->get(ExtraPropertiesFormBuilderModifier::DEFAULT_FALLBACK_TAB);
        $this->assertTrue($tab->has(ExtraPropertiesFormBuilderModifier::FALLBACK_FORM_SECTION), 'An extra_properties section should be created in the tab.');
        $this->assertTrue($tab->get(ExtraPropertiesFormBuilderModifier::FALLBACK_FORM_SECTION)->has(self::FIELD_NAME));
        $this->assertFalse($builder->has(self::FIELD_NAME), 'Field must not be added at root on a NavigationTab form.');
    }

    public function testContainerPathWithoutModeAppendsInsideContainer(): void
    {
        $builder = $this->createProductLikeFormBuilder();
        $this->makeModifier($this->definition('product:options'))->apply($builder, 'product', null);

        $this->assertFalse($builder->has(self::FIELD_NAME), 'Field must not leak to the root for a container path.');
        $this->assertTrue($builder->get('options')->has(self::FIELD_NAME), 'Field should be appended inside the options container.');
    }

    public function testAnchorPathBeforeInsertsBeforeAnchorInParent(): void
    {
        $builder = $this->createProductLikeFormBuilder();
        $this->makeModifier($this->definition('product:options.suppliers:before'))->apply($builder, 'product', null);

        $this->assertSame(
            ['visibility', self::FIELD_NAME, 'suppliers'],
            array_keys($builder->get('options')->all())
        );
    }

    public function testAnchorPathAfterInsertsAfterAnchorInParent(): void
    {
        $builder = $this->createProductLikeFormBuilder();
        $this->makeModifier($this->definition('product:options.suppliers:after'))->apply($builder, 'product', null);

        $this->assertSame(
            ['visibility', 'suppliers', self::FIELD_NAME],
            array_keys($builder->get('options')->all())
        );
    }

    public function testMissingContainerSegmentThrows(): void
    {
        $builder = $this->createProductLikeFormBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('options.nonexistent');

        $this->makeModifier($this->definition('product:options.nonexistent'))->apply($builder, 'product', null);
    }

    public function testMissingAnchorThrows(): void
    {
        $builder = $this->createProductLikeFormBuilder();

        $this->expectException(InvalidArgumentException::class);

        $this->makeModifier($this->definition('product:options.ghost:before'))->apply($builder, 'product', null);
    }

    /**
     * Guards the bug that motivated this change: the builder placed the field in one node while the
     * persister read it from another. Builder + persister must agree for both placement styles.
     *
     * @dataProvider roundTripProvider
     */
    public function testBuilderAndPersisterAgreeOnFieldLocation(string $formEntry): void
    {
        $definition = $this->definition($formEntry);

        // Build the form exactly as the modifier would in the BO.
        $builder = $this->createProductLikeFormBuilder();
        $this->makeModifier($definition)->apply($builder, 'product', null);
        $form = $builder->getForm();

        $form->submit([
            'options' => [
                'visibility' => '1',
                'suppliers' => '2',
                self::FIELD_NAME => 'dangerous-value',
            ],
        ]);

        $writer = $this->createMock(ExtraPropertyWriterInterface::class);
        $captured = null;
        $writer->expects($this->once())
            ->method('writeAll')
            ->willReturnCallback(function (string $entity, string $pk, int $id, array $valuesByModule) use (&$captured): void {
                $captured = $valuesByModule;
            });

        $persister = new ExtraPropertiesFormDataPersister(
            $this->repositoryReturning($definition),
            $writer,
            $this->shopContext(),
        );

        $persister->persist($form, 'product', 5);

        $this->assertSame(
            ['demoextrafield' => ['is_dangerous' => 'dangerous-value']],
            $captured,
            'The persister must read the value from the same node the builder placed the field in.'
        );
    }

    public function roundTripProvider(): array
    {
        return [
            'container path' => ['product:options'],
            'anchor before' => ['product:options.suppliers:before'],
            'anchor after' => ['product:options.suppliers:after'],
        ];
    }

    private function definition(string $formEntry): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: [$formEntry],
            formFieldType: TextType::class,
            labelWording: 'Dangerous product',
        );
    }

    private function makeModifier(ExtraPropertyDefinition $definition): ExtraPropertiesFormBuilderModifier
    {
        $translator = $this->createMock(\Symfony\Contracts\Translation\TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return new ExtraPropertiesFormBuilderModifier(
            $this->repositoryReturning($definition),
            $this->createMock(ExtraPropertyReaderInterface::class),
            $translator,
            $this->shopContext(),
            new FormBuilderModifier(),
        );
    }

    private function repositoryReturning(ExtraPropertyDefinition $definition): ExtraPropertyDefinitionRepositoryInterface
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection([$definition]));

        return $repository;
    }

    private function shopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::allShops());

        return $shopContext;
    }

    private function createSimpleFormBuilder(): FormBuilderInterface
    {
        $builder = $this->createFormBuilder(FormType::class);
        $builder->add('name', TextType::class);

        return $builder;
    }

    private function createNavigationTabFormBuilder(): FormBuilderInterface
    {
        $builder = $this->createFormBuilder(FormType::class);
        // A NavigationTabType child marks the form as a tabbed form (isNavigationTabForm()).
        $builder->add('nav', NavigationTabType::class);
        $builder->add('name', TextType::class);

        return $builder;
    }

    private function createProductLikeFormBuilder(): FormBuilderInterface
    {
        $builder = $this->createFormBuilder(FormType::class);
        $builder->add('options', FormType::class);
        $options = $builder->get('options');
        $options->add('visibility', TextType::class);
        $options->add('suppliers', TextType::class);

        return $builder;
    }
}
