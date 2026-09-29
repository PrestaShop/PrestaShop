<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Grid;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Grid\ExtraPropertiesGridDefinitionModifier;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterInterface;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExtraPropertiesGridDefinitionModifierTest extends TestCase
{
    private const GRID_ID = 'product';

    /**
     * @dataProvider provideColumnClassPerType
     *
     * @param class-string<ColumnInterface> $expectedColumnClass
     */
    public function testColumnClassAndFilterTypeDependOnLogicalType(
        ExtraPropertyType $type,
        string $expectedColumnClass,
        string $expectedFilterType,
    ): void {
        $extraDefinition = $this->definition('prop', type: $type);
        $grid = $this->buildGrid();

        $this->buildModifier($extraDefinition)->apply($grid, self::GRID_ID);

        $column = $this->findColumn($grid, 'extra_mymodule_prop');
        $this->assertInstanceOf($expectedColumnClass, $column);
        $this->assertSame('Translated Label', $column->getName());
        $this->assertSame('extra_mymodule_prop', $column->getOptions()['field']);

        $filter = $grid->getFilters()->all()['extra_mymodule_prop'] ?? null;
        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertSame($expectedFilterType, $filter->getType());
        $this->assertSame('extra_mymodule_prop', $filter->getAssociatedColumn());
        $this->assertSame(['required' => false], $filter->getTypeOptions());
    }

    public static function provideColumnClassPerType(): iterable
    {
        yield 'bool' => [ExtraPropertyType::BOOL, ToggleColumn::class, YesAndNoChoiceType::class];
        yield 'date' => [ExtraPropertyType::DATE, DateTimeColumn::class, TextType::class];
        yield 'string' => [ExtraPropertyType::STRING, DataColumn::class, TextType::class];
        yield 'int' => [ExtraPropertyType::INT, DataColumn::class, TextType::class];
        yield 'float' => [ExtraPropertyType::FLOAT, DataColumn::class, TextType::class];
        yield 'html' => [ExtraPropertyType::HTML, DataColumn::class, TextType::class];
        yield 'choice' => [ExtraPropertyType::CHOICE, DataColumn::class, TextType::class];
    }

    public function testFormTypeOverrideDoesNotChangeTheColumnClass(): void
    {
        $extraDefinition = $this->definition('flag', type: ExtraPropertyType::BOOL)
            ->withOverrides(['formType' => TextType::class]);
        $grid = $this->buildGrid();

        $this->buildModifier($extraDefinition)->apply($grid, self::GRID_ID);

        $this->assertInstanceOf(ToggleColumn::class, $this->findColumn($grid, 'extra_mymodule_flag'));
        $this->assertSame(YesAndNoChoiceType::class, $grid->getFilters()->all()['extra_mymodule_flag']->getType());
    }

    public function testBoolColumnTargetsToggleEndpointWithoutClientControlledScopeParams(): void
    {
        $grid = $this->buildGrid();

        $this->buildModifier($this->definition('flag', type: ExtraPropertyType::BOOL))->apply($grid, self::GRID_ID);

        $options = $this->findColumn($grid, 'extra_mymodule_flag')->getOptions();
        $this->assertSame('admin_common_extra_properties_toggle', $options['route']);
        $this->assertSame('entityId', $options['route_param_name']);
        $this->assertSame('id_product', $options['primary_field']);
        // No shopId / _legacy_controller: both are resolved server-side, never trusted from the URL.
        $this->assertSame(
            ['entityName' => 'product', 'moduleName' => 'mymodule', 'propertyName' => 'flag'],
            $options['extra_route_params']
        );
    }

    public function testJsonDefinitionsAreSkipped(): void
    {
        $grid = $this->buildGrid();

        $this->buildModifier($this->definition('payload', type: ExtraPropertyType::JSON))->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'actions'], $this->columnIds($grid));
        $this->assertSame([], $grid->getFilters()->all());
    }

    public function testColumnWithoutPlacementIsInsertedBeforeActions(): void
    {
        $grid = $this->buildGrid();

        $this->buildModifier($this->definition('note'))->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'extra_mymodule_note', 'actions'], $this->columnIds($grid));
    }

    public function testColumnWithoutPlacementIsAppendedWhenGridHasNoActionsColumn(): void
    {
        $grid = $this->buildGrid(withActions: false);

        $this->buildModifier($this->definition('note'))->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'extra_mymodule_note'], $this->columnIds($grid));
    }

    /**
     * @dataProvider providePlacement
     *
     * @param list<string> $expectedIds
     */
    public function testExplicitPlacement(string $gridEntry, bool $withActions, array $expectedIds): void
    {
        $grid = $this->buildGrid($withActions);

        $this->buildModifier($this->definition('note', gridEntry: $gridEntry))->apply($grid, self::GRID_ID);

        $this->assertSame($expectedIds, $this->columnIds($grid));
    }

    public static function providePlacement(): iterable
    {
        yield 'before' => ['product:name:before', true, ['id_product', 'extra_mymodule_note', 'name', 'actions']];
        yield 'after' => ['product:id_product:after', true, ['id_product', 'extra_mymodule_note', 'name', 'actions']];
        yield 'mode defaults to after' => ['product:name', true, ['id_product', 'name', 'extra_mymodule_note', 'actions']];
        yield 'after last column' => ['product:actions:after', true, ['id_product', 'name', 'actions', 'extra_mymodule_note']];
        yield 'unknown anchor falls back before actions' => ['product:missing:before', true, ['id_product', 'name', 'extra_mymodule_note', 'actions']];
        yield 'unknown anchor without actions appends' => ['product:missing:after', false, ['id_product', 'name', 'extra_mymodule_note']];
    }

    public function testMultipleDefinitionsKeepRegistrationOrderBeforeActions(): void
    {
        $grid = $this->buildGrid();

        $this->buildModifier(
            $this->definition('first'),
            $this->definition('second', type: ExtraPropertyType::BOOL),
        )->apply($grid, self::GRID_ID);

        $this->assertSame(
            ['id_product', 'name', 'extra_mymodule_first', 'extra_mymodule_second', 'actions'],
            $this->columnIds($grid)
        );
        $this->assertCount(2, $grid->getFilters()->all());
    }

    public function testDefinitionsNotAssociatedToTheGridAreIgnored(): void
    {
        $grid = $this->buildGrid();

        $this->buildModifier(
            $this->definition('other_grid', gridEntry: 'category'),
            $this->definition('no_grid', gridEntry: null),
            $this->definition('note'),
        )->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'extra_mymodule_note', 'actions'], $this->columnIds($grid));
        $this->assertSame(['extra_mymodule_note'], array_keys($grid->getFilters()->all()));
    }

    public function testExistingColumnIdIsNotOverriddenNorFiltered(): void
    {
        $grid = $this->buildGrid();
        $existing = (new DataColumn('extra_mymodule_note'))->setName('Existing');
        $grid->getColumns()->addBefore('actions', $existing);

        $this->buildModifier($this->definition('note'))->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'extra_mymodule_note', 'actions'], $this->columnIds($grid));
        $this->assertSame($existing, $this->findColumn($grid, 'extra_mymodule_note'));
        $this->assertSame([], $grid->getFilters()->all());
    }

    public function testShopFilterReceivesGridDefinitionsAndContextConstraint(): void
    {
        $kept = $this->definition('kept');
        $filteredOut = $this->definition('filtered_out');
        $constraint = ShopConstraint::shop(3);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn($constraint);

        $definitionShopFilter = $this->createMock(ExtraPropertyDefinitionShopFilterInterface::class);
        $definitionShopFilter
            ->expects($this->once())
            ->method('filterByShopConstraint')
            ->with(
                $this->callback(static fn (ExtraPropertyDefinitionCollection $c): bool => 2 === $c->count()),
                $this->identicalTo($constraint)
            )
            ->willReturn(new ExtraPropertyDefinitionCollection([$kept]));

        $grid = $this->buildGrid();
        $modifier = new ExtraPropertiesGridDefinitionModifier(
            $this->repository($kept, $filteredOut, $this->definition('other_grid', gridEntry: 'category')),
            $this->translator(),
            $shopContext,
            $definitionShopFilter,
        );

        $modifier->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'extra_mymodule_kept', 'actions'], $this->columnIds($grid));
    }

    public function testNothingIsTranslatedWhenNoDefinitionApplies(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->never())->method('trans');
        $grid = $this->buildGrid();

        $modifier = new ExtraPropertiesGridDefinitionModifier(
            $this->repository($this->definition('other_grid', gridEntry: 'category')),
            $translator,
            $this->shopContext(),
            $this->passThroughShopFilter(),
        );
        $modifier->apply($grid, self::GRID_ID);

        $this->assertSame(['id_product', 'name', 'actions'], $this->columnIds($grid));
    }

    public function testLabelIsTranslatedWithDeclaredDomainOrAdminGlobal(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->willReturnCallback(static fn (string $id, array $parameters, ?string $domain): string => $id . '@' . $domain);
        $grid = $this->buildGrid();

        $modifier = new ExtraPropertiesGridDefinitionModifier(
            $this->repository(
                $this->definition('with_domain', labelDomain: 'Modules.Mymodule.Admin'),
                $this->definition('without_domain'),
            ),
            $translator,
            $this->shopContext(),
            $this->passThroughShopFilter(),
        );
        $modifier->apply($grid, self::GRID_ID);

        $this->assertSame('Label@Modules.Mymodule.Admin', $this->findColumn($grid, 'extra_mymodule_with_domain')->getName());
        $this->assertSame('Label@Admin.Global', $this->findColumn($grid, 'extra_mymodule_without_domain')->getName());
    }

    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function buildModifier(ExtraPropertyDefinition ...$definitions): ExtraPropertiesGridDefinitionModifier
    {
        return new ExtraPropertiesGridDefinitionModifier(
            $this->repository(...$definitions),
            $this->translator(),
            $this->shopContext(),
            $this->passThroughShopFilter(),
        );
    }

    private function repository(ExtraPropertyDefinition ...$definitions): ExtraPropertyDefinitionRepositoryInterface
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection(array_values($definitions)));

        return $repository;
    }

    private function translator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturn('Translated Label');

        return $translator;
    }

    private function shopContext(): ShopContext
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));

        return $shopContext;
    }

    private function passThroughShopFilter(): ExtraPropertyDefinitionShopFilterInterface
    {
        $definitionShopFilter = $this->createMock(ExtraPropertyDefinitionShopFilterInterface::class);
        $definitionShopFilter->method('filterByShopConstraint')->willReturnArgument(0);

        return $definitionShopFilter;
    }

    private function buildGrid(bool $withActions = true): GridDefinition
    {
        $columns = (new ColumnCollection())
            ->add((new DataColumn('id_product'))->setName('ID')->setOptions(['field' => 'id_product']))
            ->add((new DataColumn('name'))->setName('Name')->setOptions(['field' => 'name']));
        if ($withActions) {
            $columns->add((new ActionColumn('actions'))->setName('Actions'));
        }

        return new GridDefinition(
            self::GRID_ID,
            'Products',
            $columns,
            new FilterCollection(),
            new GridActionCollection(),
            new BulkActionCollection(),
            new ViewOptionsCollection()
        );
    }

    /**
     * @return list<string>
     */
    private function columnIds(GridDefinition $grid): array
    {
        $ids = [];
        foreach ($grid->getColumns() as $column) {
            $ids[] = $column->getId();
        }

        return $ids;
    }

    private function findColumn(GridDefinition $grid, string $id): ColumnInterface
    {
        foreach ($grid->getColumns() as $column) {
            if ($id === $column->getId()) {
                return $column;
            }
        }

        $this->fail(sprintf('Column "%s" not found in grid.', $id));
    }

    private function definition(
        string $propertyName,
        ExtraPropertyType $type = ExtraPropertyType::STRING,
        ?string $gridEntry = self::GRID_ID,
        ?string $labelDomain = null,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            type: $type,
            moduleName: 'mymodule',
            enumValues: ExtraPropertyType::CHOICE === $type ? ['a', 'b'] : null,
            associatedGrids: null !== $gridEntry ? [$gridEntry] : null,
            labelWording: 'Label',
            labelDomain: $labelDomain,
        );
    }
}
