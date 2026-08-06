---
name: create-grid-query-builder
description: >
  Create the Doctrine DBAL QueryBuilder that fetches grid rows, and optionally a
  GridDataFactory decorator for post-processing. The column aliases must exactly
  match the column IDs in the Grid Definition. Trigger:
  "create grid query builder for {Domain}".
needs: [create-grid-definition]
produces: "{Domain}QueryBuilder.php + optional GridDataFactory decorator"
subagent: optional
---

# create-grid-query-builder

Read `@.ai/Component/Grid/CONTEXT.md` for the factory trilogy and GridDataFactory decoration pattern.

## 1. Query Builder

Create `src/Adapter/{Domain}/Grid/Query/{Domain}QueryBuilder.php` implementing `DoctrineQueryBuilderInterface` or extending `AbstractDoctrineQueryBuilder`:

Two required methods:

- `getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder` — returns rows for the current page
- `getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder` — returns total count (no LIMIT/OFFSET)

### Building the query

1. Base query: `SELECT c.id_{domain}, c.name, c.active FROM ps_{domain} c`
2. For each filter in `$searchCriteria->getFilters()`, add a WHERE clause with parameterized values
3. Apply sorting: map `$searchCriteria->getOrderBy()` to actual column expressions
4. Apply pagination: `->setFirstResult($offset)->setMaxResults($limit)` (search query only, not count)
5. For multilingual columns: LEFT JOIN the `_lang` table with the current language ID

**Reference:** `src/Core/Grid/Query/LanguageQueryBuilder.php` (simple), `src/Adapter/Manufacturer/QueryBuilder/ManufacturerQueryBuilder.php` (with joins)

## 2. GridDataFactory (standard)

For most grids, the standard `DoctrineGridDataFactory` is sufficient — it calls the query builder automatically. Just wire it in DI:

```yaml
prestashop.core.grid.data.factory.{domain}:
    class: 'PrestaShop\PrestaShop\Core\Grid\Data\Factory\DoctrineGridDataFactory'
    arguments:
        - '@prestashop.core.admin.{domain}.query_builder'
        # ... hook dispatcher, query parser, etc.
```

## 3. GridDataFactory decorator (if post-processing needed)

When grid data needs transformation after query execution (e.g. resolving image URLs from IDs, formatting computed columns), create a decorator:

- Create a class implementing `GridDataFactoryInterface`
- Inject the original `GridDataFactory`
- In `getData(SearchCriteriaInterface $searchCriteria)`: call the wrapped factory, then modify the `RecordCollection`
- Register with `decorates:` in DI YAML

```php
class {Domain}GridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $dataFactory,
    ) {}

    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $gridData = $this->dataFactory->getData($searchCriteria);
        $modifiedRecords = $this->applyModifications($gridData->getRecords());
        return new GridData($modifiedRecords, $gridData->getRecordsTotal(), $gridData->getQuery());
    }
}
```

**Reference:** Check existing `*GridDataFactory` decorators in `src/Adapter/` for examples.

## Rules

Conventions (column alias matching, parameterized queries, PK aliasing, count query rules, decoration preference) are in [Grid/CONTEXT.md](../../CONTEXT.md#query-builders).
