# The Faceted Search Architecture

This document describes the faceted search architecture that is being implemented in PrestaShop 1.7.

**Please also read the extensive comments in `classes/controller/ProductListingFrontControllerCore`, as this document is only a high-level explanation.**

The target audience is anybody who wants to develop a well-integrated search module that replaces the way the PrestaShop core searches for products.

At the moment this proof of concept works only on the `CategoryController` but it is easy to extend to the other product controllers (manufacturers, supplier...).

## Why, oh why?

Efficient product search is at the heart of E-Commerce. Customers need to find what they're looking for easily.

When developing the `StarterTheme` we realized the way `blocklayered` and similar modules interact with the theme is very complicated and hard to extend. The modules try to emulate the `CategoryController` and fetch templates from the theme without any guarantee of the templates being there.

If we change the behavior of the `CategoryController` or rename a template, then all `blocklayered`-like modules need to be adapted. If your theme doesn't have a file called `product-list.tpl` then the module fails, etc.

We have analyzed the way search modules work and we offer a set of standard objects and behaviors that allow us to reason about faceted search and improve it. This is all based on what we've observed, we're mostly just putting a name on things and giving guidelines.

## Key Concepts

### Overview of the rendering process for displaying products on a category page

1. The core `CategoryController` executes a hook basically asking modules "hey, does anybody want to fetch the products for the category with `id_category` === 4 or should I do it myself?"
2. A module (e.g. `blocklayered`) responds by returning an instance of a `ProductSearchProviderInterface` of its choosing
3. The `CategoryController` notices the `ProductSearchProviderInterface` returned by the module and uses it to get the products (this is the equivalent of what `hookActionProductListOverride` did, only we work with well defined objects that are easy to reason about).
4. The search provider returns a `ProductSearchResult`, it contains:
    - the products, obviously (which may just be an array like `[['id_product' => 2], ['id_product' => 3]]` - the core will add the missing data!)
    - the pagination information (total number of pages, total number or results, etc.) cleanly wrapped inside a `PaginationResult`
    - the new, updated filters
    - the sort options that are supported to sort the list (array of `SortOrder`s)
5. The `CategoryController` hydrates the product list, formats it, renders it. It also renders the filters, the pagination, and the sort options (price ascending, etc.).

Bottom line is, the search module only needs to worry about two things:
- executing a pure database query (internal or external database) that returns a list of product ids (no more `getProductProperties`, `addColorsToProductList`, etc.)
- optionally (if it wants to produce nice URLs), encode and decode the filters in a way that fits inside a URL

With this we reduced the size of the code in `blocklayered` by a factor of about 2.

### Terminology

We did not make the words up, see for instance this article about [Facts vs Filters](http://www.nngroup.com/articles/filters-vs-facets/).

#### Facets and Filters

##### Filters

We call a filter any assertion that can be used to filter a list of products and **does not contain logical operators** such as "and" or "or" when expressed in plain English.

For instance "Blue products" is a filter. "Red or blue products" is **not** a filter. It's a facet...

A Filter is represented by the `PrestaShop\PrestaShop\Core\Business\Product\Search\Filter` class.

##### Facets

We call a facet a set of filters combined with logical operators.

For instance "Blue products or red products" is a facet.

Filters within a facet may be active or not, and are usually combined with the "or" operator even though it is defined by the implementation and not necessarily so. Still, there seems to be a strong UX convention that filters inside a facet are combined with "or", meaning for instance that if I check the "Blue" and the "Red" filter I won't get products that are both blue and red, but a mix of blue products and red products.

A facet is represented by the `PrestaShop\PrestaShop\Core\Business\Product\Search\Facet` class. It is basically a collection of `Filter`s.

### The `ProductSearchQuery` object

We introduce the `PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery` object to hold all search query information.

Basically, this object contains:
- something that tells modules where the query came from (only `id_category` at the moment, but we may add `id_supplier` for `SupplierController` etc.). This is the minimal filter that the search module is supposed to implement.
- an array of `Facet`s
- the `SortOrder` that is requested
- the `page` number that is requested
- the `resultsPerPage`, i.e. the number of products per page that is expected

### The search delegation mechanism

In order for modules to replace the core search mechanism, we introduce a delegation mechanism in the form of the `productSearchProvider` hook.

The hook is executed with a `ProductSearchQuery $query` param, which allows modules to return an instance of a `ProductSearchProviderInterface` that is able to handle the query.
