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
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormBuilderModifier;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyTypeCompatibility;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShopBundle\Form\Admin\Type\NavigationTabType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\FormBuilderModifier;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\BrokenFixtureType;
use Tests\Unit\Core\ExtraProperty\Catalog\Fixtures\HeadingLabelFixtureType;

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

    /**
     * Read-side enforcement of ExtraPropertyFormOptionsPolicy: a definition carrying options the
     * policy refuses (as a row written straight into the registry would) renders its declared type
     * without the refused options, and says so in the log.
     */
    public function testDeniedOptionsAreDroppedAndLoggedOnRead(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            formType: TextPreviewType::class,
            formOptions: ['allow_html' => true, 'attr' => ['class' => 'ok', 'onfocus' => 'alert(1)']],
            labelWording: 'Dangerous product',
        );
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition, $logger)->apply($builder, 'product', null);

        $field = $builder->get(self::FIELD_NAME);
        $this->assertInstanceOf(TextPreviewType::class, $field->getType()->getInnerType(), 'The declared type is kept: the danger is in the options.');
        $this->assertSame(['class' => 'ok'], $field->getOption('attr'), 'Only the refused attribute is dropped.');
        $this->assertFalse($field->getOption('allow_html'), 'allow_html is dropped, so the type falls back to its escaping default.');
    }

    /**
     * An option the policy does not know is the declared type's business: when the type refuses it
     * (a tampered row, or a type whose options changed since the definition was saved), the field is
     * dropped and logged instead of taking the whole entity form down.
     */
    public function testAFieldWhoseOptionsDoNotBuildIsDroppedAndLoggedOnRead(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            formType: TextType::class,
            formOptions: ['not_an_option_of_text_type' => true],
            labelWording: 'Dangerous product',
        );
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition, $logger)->apply($builder, 'product', null);

        $this->assertFalse($builder->has(self::FIELD_NAME), 'The unbuildable field is removed; the form itself survives.');
    }

    /**
     * A null option must reach the form type: it overrides the type's own default, which is how
     * demoextrafield keeps its supplier field from rendering a heading instead of a label.
     */
    public function testANullOptionOverridesTheFormTypeDefault(): void
    {
        $withNull = $this->headingLabelDefinition(['label_tag_name' => null]);
        $withoutOption = $this->headingLabelDefinition(null);

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($withNull)->apply($builder, 'product', null);
        $this->assertNull($builder->get(self::FIELD_NAME)->getOption('label_tag_name'));

        $untouched = $this->createSimpleFormBuilder();
        $this->makeModifier($withoutOption)->apply($untouched, 'product', null);
        $this->assertSame('h3', $untouched->get(self::FIELD_NAME)->getOption('label_tag_name'));
    }

    /**
     * @param array<string, mixed>|null $formOptions
     */
    private function headingLabelDefinition(?array $formOptions): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            formType: HeadingLabelFixtureType::class,
            formOptions: $formOptions,
            labelWording: 'Dangerous product',
        );
    }

    /**
     * A custom form type is module code: a bug thrown while it builds (not an options-resolver
     * error) must not take the host form down either.
     */
    public function testAFieldWhoseTypeThrowsWhileBuildingIsDroppedAndLoggedOnRead(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            formType: BrokenFixtureType::class,
            labelWording: 'Dangerous product',
        );
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition, $logger)->apply($builder, 'product', null);

        $this->assertFalse($builder->has(self::FIELD_NAME));
    }

    /**
     * A field of the same name already present in the host form is never the extra property's:
     * it is neither replaced, nor resolved and dropped, whatever the definition's options.
     */
    public function testAHostFieldWithTheSameNameIsLeftUntouched(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            formType: TextType::class,
            formOptions: ['not_an_option_of_text_type' => true],
            labelWording: 'Dangerous product',
        );
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $builder = $this->createSimpleFormBuilder();
        $builder->add(self::FIELD_NAME, TextType::class, ['label' => 'Host field']);
        $this->makeModifier($definition, $logger)->apply($builder, 'product', null);

        $this->assertTrue($builder->has(self::FIELD_NAME));
        $this->assertSame('Host field', $builder->get(self::FIELD_NAME)->getOption('label'));
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
            formType: TextType::class,
            constraints: [$url],
            labelWording: 'Dangerous product',
        );

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition)->apply($builder, 'product', null);

        $constraints = $builder->get(self::FIELD_NAME)->getOption('constraints');

        // The definition's constraints are attached verbatim to the field, followed by the
        // ALWAYS-attached implicit type-compatibility safety net (the same rule-set as the
        // registry's default check and validateValue()).
        $this->assertCount(2, $constraints);
        $this->assertSame($url, $constraints[0]);
        $this->assertInstanceOf(ExtraPropertyTypeCompatibility::class, $constraints[1]);
        $this->assertSame(ExtraPropertyType::STRING, $constraints[1]->type);
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
            formType: TextType::class,
            constraints: [$all],
            labelWording: 'Video link',
        );

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition)->apply($builder, 'product', null);

        $field = $builder->get(self::FIELD_NAME);
        // LANG constraints validate the whole [id_lang => value] array, so they attach to the OUTER TranslatableType
        // — NOT nested per-language under the children's options (which is where per-element rules used to live).
        // The implicit type-compatibility net follows, wrapped in Assert\All (per-language leaves).
        $constraints = $field->getOption('constraints');
        $this->assertCount(2, $constraints);
        $this->assertSame($all, $constraints[0]);
        $this->assertInstanceOf(\Symfony\Component\Validator\Constraints\All::class, $constraints[1]);
        $this->assertInstanceOf(ExtraPropertyTypeCompatibility::class, $constraints[1]->constraints[0]);
        $this->assertArrayNotHasKey('constraints', (array) $field->getOption('options'));
    }

    /**
     * The functional payoff of the implicit constraint: a value the registry would refuse
     * as a default is refused INLINE by the form too, with zero declared constraints —
     * here invalid JSON typed in the free-text textarea, previously stored verbatim.
     */
    public function testInvalidJsonIsRefusedInlineWithoutDeclaredConstraints(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            type: ExtraPropertyType::JSON,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            labelWording: 'Meta',
        );

        // csrf_protection off: submitting without a _token would otherwise invalidate the
        // ROOT form and mask what the FIELD reports — the object under test here.
        $builder = $this->createFormBuilder(FormType::class, ['csrf_protection' => false]);
        $this->makeModifier($definition)->apply($builder, 'product', null);
        $form = $builder->getForm();
        $form->submit([self::FIELD_NAME => '{invalid']);

        $this->assertFalse($form->isValid());
        $errors = $form->get(self::FIELD_NAME)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('"json" field type', (string) $errors->current()->getMessage());

        // A valid JSON string sails through.
        $builder = $this->createFormBuilder(FormType::class, ['csrf_protection' => false]);
        $this->makeModifier($definition)->apply($builder, 'product', null);
        $form = $builder->getForm();
        $form->submit([self::FIELD_NAME => '{"tier":"bronze"}']);

        $this->assertTrue($form->isValid());
        $this->assertCount(0, $form->get(self::FIELD_NAME)->getErrors());
    }

    public function testDefaultFormTypeIsDerivedFromLogicalTypeWhenNoOverride(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'is_dangerous',
            type: ExtraPropertyType::BOOL,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            associatedForms: ['product'],
            labelWording: 'Dangerous product',
        );

        $builder = $this->createSimpleFormBuilder();
        $this->makeModifier($definition)->apply($builder, 'product', null);

        // No formType declared: the type map supplies the widget (BOOL => SwitchType), not TextType.
        $this->assertInstanceOf(
            \PrestaShopBundle\Form\Admin\Type\SwitchType::class,
            $builder->get(self::FIELD_NAME)->getType()->getInnerType()
        );
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

        $definitionShopFilter = $this->createMock(ExtraPropertyDefinitionShopFilterInterface::class);
        $definitionShopFilter->method('filterByShopConstraint')->willReturnArgument(0);

        $persister = new ExtraPropertiesFormDataPersister(
            $this->repositoryReturning($definition),
            $writer,
            $this->shopContext(),
            $definitionShopFilter,
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
            formType: TextType::class,
            labelWording: 'Dangerous product',
        );
    }

    private function makeModifier(ExtraPropertyDefinition $definition, ?LoggerInterface $logger = null): ExtraPropertiesFormBuilderModifier
    {
        $translator = $this->createMock(\Symfony\Contracts\Translation\TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $definitionShopFilter = $this->createMock(ExtraPropertyDefinitionShopFilterInterface::class);
        $definitionShopFilter->method('filterByShopConstraint')->willReturnArgument(0);

        return new ExtraPropertiesFormBuilderModifier(
            $this->repositoryReturning($definition),
            $this->createMock(ExtraPropertyReaderInterface::class),
            $translator,
            $this->shopContext(),
            new FormBuilderModifier(),
            new ExtraPropertyFormTypeMap(),
            $definitionShopFilter,
            $logger ?? new NullLogger(),
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
