# FacetedSearch Component

## Purpose

Layered/faceted product filtering on the storefront: Core defines the contracts (`ProductSearchProviderInterface`, `Facet`, `Filter`), the `ps_facetedsearch` native module provides the concrete implementation. Does not handle back-office search or grid filtering.

## Layers

| Layer | Path |
|-------|------|
| Core contracts + value objects | `src/Core/Product/Search/` |
| Module implementation | `modules/ps_facetedsearch/src/` |

## Non-obvious patterns

- The Core layer owns the contracts (`ProductSearchProviderInterface`, `FacetsRendererInterface`, `Facet`, `Filter`) but has zero concrete implementation — the module is mandatory for faceted search to work
- `Filter::nextEncodedFacets` encodes the entire URL filter state as a single string — used for shareable/bookmarkable filter URLs
- 8 filter types defined in `Filters/Converter`: `attribute_group`, `availability`, `category`, `condition`, `feature`, `manufacturer`, `price`, `weight`
- Cache invalidation is hook-driven: `HookDispatcher` listens to product/attribute/feature changes

## Canonical examples

- `src/Core/Product/Search/ProductSearchProviderInterface.php`
- `src/Core/Product/Search/Facet.php`
- `modules/ps_facetedsearch/src/Product/SearchProvider.php`

