# New Pricing Architecture - Specification

## Context

PrestaShop's current pricing system suffers from:
- Monolithic legacy code (`Product::getPriceStatic` with 17 parameters, recursive static calls)
- Float-based arithmetic causing rounding errors (~50 open bugs in "Taxes and Prices")
- Business logic coupled with persistence (ObjectModel)
- Untestable architecture (static methods, global state, hidden dependencies)

This spec defines a clean, composable, fully-tested pricing architecture in `PrestaShop\PrestaShop\Core\Pricing` that replaces the legacy system behind a feature flag. The architecture prioritizes:
- `DecimalNumber` exclusively (no native floats)
- Small, independent, testable calculator components
- Symfony DI with tagged services and priority-based chaining
- Debug-only audit trail (transparent to calculators)
- Module extensibility from day one
- FO + BO compatibility via shared service definitions
- Dual-context support: same architecture for Cart (FO) and Order (BO) with swappable providers

**Related issues:** #40948, #40949, #40951, #40952, #41014, #40979
**Related epics:** #9703, #19445
**Related PRs:** #41039 (Phase 1)

---

## 1. Namespace Structure

```
src/Core/Pricing/
├── Exception/
│   ├── PricingException.php              # Base exception, extends CoreException
│   ├── InvalidTaxRateException.php       # Negative tax rate
│   └── ProductPriceNotFoundException.php  # Product not found in provider
├── PricingConstants.php                   # Shared constants (INTERMEDIATE_PRECISION)
├── ValueObject/
│   ├── TaxablePriceInterface.php         # Read-only contract for any price with tax info
│   ├── TaxablePrice.php                  # Mutable, auto-sync tax incl/excl (implements TaxablePriceInterface)
│   ├── ImmutableTaxablePrice.php         # Immutable, stores independently computed values (e.g. after rounding)
│   ├── TaxRate.php                       # Wraps a DecimalNumber rate, validates >= 0
│   ├── PriceModification.php             # Debug: single modification record
│   └── PriceBreakdown.php               # Debug: collection of PriceModification steps
├── Product/
│   ├── ProductPriceInterface.php
│   ├── ProductPrice.php                  # Lightweight DTO (no tracking)
│   ├── TrackedProductPrice.php           # Debug DTO (auto-tracks modifications via debug_backtrace)
│   ├── Calculator/
│   │   ├── ProductCalculatorInterface.php
│   │   ├── ProductCalculator.php         # Main entry point, delegates to sub-calculators pipeline
│   │   ├── BaseProductCalculator.php
│   │   ├── CombinationCalculator.php
│   │   ├── SpecificPriceCalculator.php
│   │   ├── GroupReductionCalculator.php
│   │   ├── TaxCalculator.php
│   │   ├── EcoTaxCalculator.php
│   │   ├── CurrencyCalculator.php
│   │   └── RoundingCalculator.php
│   └── Provider/
│       ├── ProductProviderInterface.php  # Returns ProductPriceData, throws ProductPriceNotFoundException
│       ├── ProductPriceData.php          # Raw pricing DTO with 4 fields from DB
│       ├── CatalogProductProvider.php    # FO: reads from ps_product + ps_product_attribute
│       └── MockProductProvider.php       # For unit tests
├── Tax/
│   ├── TaxProviderInterface.php
│   ├── DatabaseTaxProvider.php
│   ├── TaxComputationMethod.php          # Enum: COMBINE, ONE_AFTER_ANOTHER
│   └── MockTaxProvider.php
├── SpecificPrice/
│   ├── SpecificPriceProviderInterface.php
│   ├── DatabaseSpecificPriceProvider.php
│   └── MockSpecificPriceProvider.php
├── Cart/
│   ├── CartPriceInterface.php
│   ├── CartPrice.php                     # Lightweight DTO
│   ├── TrackedCartPrice.php              # Debug DTO
│   ├── Calculator/
│   │   ├── CartCalculatorInterface.php
│   │   ├── CartCalculator.php
│   │   ├── ProductTotalCalculator.php
│   │   ├── ShippingCalculator.php
│   │   ├── WrappingCalculator.php
│   │   └── Discount/
│   │       ├── PercentageDiscountCalculator.php
│   │       ├── AmountDiscountCalculator.php
│   │       ├── FreeShippingDiscountCalculator.php
│   │       └── FreeGiftDiscountCalculator.php
│   ├── Provider/
│   │   ├── CartProductProviderInterface.php
│   │   ├── DatabaseCartProductProvider.php
│   │   └── MockCartProductProvider.php
│   ├── CartManager.php
│   ├── CartPersisterInterface.php
│   └── Checker/
│       ├── CartCheckerInterface.php
│       ├── CompositeCartChecker.php
│       ├── ProductPriceChecker.php
│       ├── DiscountChecker.php
│       ├── TaxRateChecker.php
│       └── ShippingChecker.php
├── Rounding/
│   ├── RoundingServiceInterface.php
│   └── RoundingService.php
├── Context/
│   ├── PriceContext.php
│   └── PriceContextFactory.php
└── Debug/
    ├── PricingRegistry.php               # Collects computed prices during request
    ├── PricingHistoryDisplayer.php       # Formats debug history for display
    └── PricingDataCollector.php          # Symfony profiler integration (in PrestaShopBundle)
```

---

## 2. Design Principles

### 2.1 No `final`, Prefer `protected`

Classes in the pricing namespace are **not** `final` and use `protected` instead of `private` for properties, methods and constants. This allows module developers to extend or override behavior when needed.

### 2.2 Mutable DTOs, Transparent Debug Tracking

Price DTOs (`ProductPrice`, `CartPrice`) are **mutable** with setters. This keeps calculator code simple — calculators just call setters on the DTO they receive.

Debug tracking is **completely transparent to calculators**:

- `ProductPrice` / `CartPrice` — lightweight, no tracking overhead
- `TrackedProductPrice` / `TrackedCartPrice` — same interface, auto-records every setter call using `debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)` to capture the calling calculator's class name and line number

The caller creates the appropriate DTO based on `kernel.debug`. Calculators are completely unaware of which implementation they're working with.

```php
// In TrackedProductPrice — automatic, no calculator involvement:
public function setUnitPrice(TaxablePrice $unitPrice): void
{
    $this->recordModification('unitPrice', $this->unitPrice, $unitPrice);
    $this->unitPrice = $unitPrice;
}

protected function recordModification(string $property, TaxablePriceInterface $previous, TaxablePriceInterface $new): void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
    $caller = $trace[2] ?? [];
    $this->breakdown->addStep(new PriceModification(
        callerClass: $caller['class'] ?? 'unknown',
        callerLine: $caller['line'] ?? 0,
        property: $property,
        previousValue: (string) $previous->getTaxExcluded(),
        newValue: (string) $new->getTaxExcluded(),
    ));
}
```

### 2.3 PriceContext as an Injected Service

`PriceContext` replaces `getPriceStatic`'s 17 parameters. It is **not** passed as a method parameter to `compute()`. Instead, it's a service created by `PriceContextFactory` and injected via constructor DI into calculators that need it.

This keeps the calculator interface minimal and avoids threading context through every method call.

### 2.4 No `supports()` Method

Calculators have a single `compute()` method. When a calculator is not relevant for the current computation (e.g., EcoTaxCalculator when eco-tax is disabled), it simply returns early, leaving the input DTO unchanged.

### 2.5 Dual-Context: Cart (FO) vs Order (BO)

The same architecture computes prices for both:
- **Cart context (FO):** reads from `ps_product`, `ps_cart_product`, `ps_specific_price`, etc.
- **Order context (BO):** reads from `ps_order_detail` (prices are stored, not computed from catalog)

This is achieved through:
- **Interface-based DI:** All providers use interfaces. Different contexts wire different implementations.
- **Separate tags per context:** `prestashop.pricing.cart.product_calculator` vs `prestashop.pricing.order.product_calculator`
- **Same calculator classes, different providers:** e.g., `BaseProductCalculator` is registered twice with different `ProductProviderInterface` implementations

```yaml
# Cart context: reads from product table
prestashop.pricing.cart.product.base_product_calculator:
  class: BaseProductCalculator
  arguments:
    $productProvider: '@...CatalogProductProvider'
  tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 100 } ]

# Order context: reads from order_detail table
prestashop.pricing.order.product.base_product_calculator:
  class: BaseProductCalculator
  arguments:
    $productProvider: '@...OrderDetailProductProvider'
  tags: [ { name: 'prestashop.pricing.order.product_calculator', priority: 100 } ]
```

Shared calculators (e.g., `RoundingCalculator`) are tagged under both contexts.

### 2.6 Custom Exceptions

All pricing exceptions extend `PricingException` which extends `CoreException`. Dedicated exceptions:
- `InvalidTaxRateException` — negative tax rate
- `ProductPriceNotFoundException` — product not found in provider (thrown instead of returning empty data)

### 2.7 Shared Constants

`PricingConstants::INTERMEDIATE_PRECISION = 20` is used consistently across all `DecimalNumber` divisions to avoid premature truncation. No hardcoded precision values.

---

## 3. Value Objects

### 3.1 TaxablePriceInterface (Read-Only Contract)

```php
namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

interface TaxablePriceInterface
{
    public function getTaxExcluded(): DecimalNumber;
    public function getTaxIncluded(): DecimalNumber;
    public function getTaxAmount(): DecimalNumber;
    public function getTaxRate(): TaxRate;
}
```

Implemented by both `TaxablePrice` (mutable, auto-sync) and `ImmutableTaxablePrice` (frozen values). This allows `ProductPriceInterface` to return either type from its getters.

### 3.2 TaxRate

```php
class TaxRate
{
    public function __construct(protected readonly DecimalNumber $rate); // Validates >= 0 via InvalidTaxRateException

    public static function zero(): self;
    public function getRate(): DecimalNumber;         // e.g. "20" for 20%
    public function getMultiplier(): DecimalNumber;   // 1 + rate/100, e.g. "1.2"
    public function equals(self $other): bool;
}
```

**Key decision:** `computeTaxAmount()` is **not** on TaxRate. Tax amount is always computed as `taxIncluded - taxExcluded` to ensure consistency with the actual stored values and avoid precision drift.

### 3.3 TaxablePrice (Mutable, Auto-Sync)

```php
class TaxablePrice implements TaxablePriceInterface
{
    protected function __construct(DecimalNumber $taxExcluded, TaxRate $taxRate); // protected — use factories

    public static function fromTaxExcluded(DecimalNumber $taxExcluded, TaxRate $taxRate): self;
    public static function fromTaxIncluded(DecimalNumber $taxIncluded, TaxRate $taxRate): self;
    public static function zero(): self;

    // Getters (from TaxablePriceInterface)
    public function getTaxExcluded(): DecimalNumber;
    public function getTaxIncluded(): DecimalNumber;
    public function getTaxAmount(): DecimalNumber;
    public function getTaxRate(): TaxRate;

    // Setters — auto-sync counterparts
    public function setTaxExcluded(DecimalNumber $taxExcluded): void;
    public function setTaxIncluded(DecimalNumber $taxIncluded): void;
    public function setTaxRate(TaxRate $taxRate): void;
}
```

**Key design decisions:**
- **Constructor is protected** — must use `fromTaxExcluded()` or `fromTaxIncluded()` factories for clarity
- **Mutable** — setters modify in place, recomputing counterparts automatically
- `taxExcluded` is the default source of truth (when taxRate changes, taxIncl is recomputed from taxExcl)
- `taxAmount` is always computed as `taxIncluded - taxExcluded` (consistent across all code paths)
- All intermediate divisions use `PricingConstants::INTERMEDIATE_PRECISION`
- **No arithmetic methods** (plus, minus, times, dividedBy) — calculators create new `TaxablePrice` instances and set them via `ProductPriceInterface` setters, which ensures proper debug tracking in `TrackedProductPrice`

### 3.4 ImmutableTaxablePrice (Frozen Values)

```php
class ImmutableTaxablePrice implements TaxablePriceInterface
{
    public function __construct(
        protected readonly DecimalNumber $taxExcluded,
        protected readonly DecimalNumber $taxIncluded,
        protected readonly DecimalNumber $taxAmount,
        protected readonly TaxRate $taxRate,
    );

    public static function fromTaxablePrice(TaxablePriceInterface $price): self;

    // Getters only (from TaxablePriceInterface) — no setters, no auto-sync
}
```

Used when both tax-excluded and tax-included have been independently computed (e.g. after rounding) and must not be recomputed from one another. `taxExcl * multiplier` may not equal `taxIncl`, which is expected for rounded display values.

### 3.5 PriceContext (Injected Service)

```php
class PriceContext
{
    public function __construct(
        protected readonly int $shopId,
        protected readonly int $currencyId,
        protected readonly int $countryId,
        protected readonly int $stateId,
        protected readonly string $zipCode,
        protected readonly int $customerId,
        protected readonly int $groupId,
        protected readonly int $quantity,
        protected readonly \DateTimeImmutable $date,
        protected readonly ?int $addressId = null,
    );
}
```

Created by `PriceContextFactory` via DI factory method. Injected into calculators via constructor DI — **not** passed as a method parameter. Deferred to Phase 6.

### 3.6 PriceModification + PriceBreakdown (Debug Only)

Used only by `TrackedProductPrice` / `TrackedCartPrice`. Calculators never interact with these directly.

```php
class PriceModification
{
    public function __construct(
        protected readonly string $callerClass,
        protected readonly int $callerLine,
        protected readonly string $property,      // e.g. "unitPrice", "originalPrice", "finalPrice"
        protected readonly string $previousValue,
        protected readonly string $newValue,
    );
}

class PriceBreakdown
{
    /** @var PriceModification[] */
    protected array $steps = [];

    public function addStep(PriceModification $step): void;
    public function getSteps(): array;
    public function count(): int;
}
```

---

## 4. Product Pricing

### 4.1 ProductPriceInterface

```php
interface ProductPriceInterface
{
    public function getProductId(): int;
    public function getCombinationId(): int;
    public function getQuantity(): int;

    public function getUnitPrice(): TaxablePrice;
    public function setUnitPrice(TaxablePrice $unitPrice): void;

    public function getOriginalPrice(): TaxablePrice;
    public function setOriginalPrice(TaxablePrice $originalPrice): void;

    public function getDiscountPrice(): TaxablePrice;
    public function setDiscountPrice(TaxablePrice $discountPrice): void;

    public function getFinalPrice(): ImmutableTaxablePrice;
    public function setFinalPrice(ImmutableTaxablePrice $finalPrice): void;
}
```

**Price fields:**
- `unitPrice` — the per-unit price (from `ps_product.unit_price` + combination impact)
- `originalPrice` — the catalog price before discounts (from `ps_product.price` + combination impact)
- `discountPrice` — the accumulated discount amount (filled by discount calculators)
- `finalPrice` — the rounded end result (`ImmutableTaxablePrice` because both tax-excluded and tax-included are independently rounded)

**Key decisions:**
- No `totalPrice` field — quantity is stored on the DTO, total computation is deferred
- `unitPrice`, `originalPrice`, `discountPrice` use concrete `TaxablePrice` (mutable, auto-sync during computation)
- Only `finalPrice` uses `ImmutableTaxablePrice` (frozen after rounding)
- `quantity` is an immutable property set at creation time

**Two implementations:**
- `ProductPrice` — lightweight, setters simply assign
- `TrackedProductPrice` — same interface, each setter also records a `PriceModification` via `debug_backtrace()`

### 4.2 ProductCalculatorInterface

```php
interface ProductCalculatorInterface
{
    public function compute(ProductPriceInterface $productPrice): void;
}
```

**No `supports()` method.** Calculators return early from `compute()` when not relevant.

### 4.3 ProductCalculator

```php
class ProductCalculator implements ProductCalculatorInterface
{
    /**
     * @param iterable<ProductCalculatorInterface> $calculators Tagged iterator, priority-sorted
     */
    public function __construct(protected readonly iterable $calculators);

    public function compute(ProductPriceInterface $productPrice): void;
}
```

Main entry point for computing a product price. Implements `ProductCalculatorInterface` like any other calculator step, but internally delegates to a priority-sorted pipeline of sub-calculators. This is an implementation detail — callers simply call `compute()`.

### 4.4 Calculator Implementations

| Calculator | Priority | Responsibility |
|---|---|---|
| `BaseProductCalculator` | 100 | Fetches raw data from provider, computes `originalPrice`, `unitPrice`, initializes `finalPrice` |
| `CombinationCalculator` | 90 | *(moved into BaseProductCalculator in Phase 1 — provider returns combination impacts directly)* |
| `SpecificPriceCalculator` | 80 | Applies specific price rules (fixed override or percentage/amount reduction) |
| `GroupReductionCalculator` | 70 | Applies customer group reduction |
| `EcoTaxCalculator` | 60 | Adds eco-tax amount |
| `TaxCalculator` | 50 | Computes tax-included price from tax-excluded using tax rules |
| `CurrencyCalculator` | 30 | Converts prices from default currency to customer's currency |
| `RoundingCalculator` | 10 | Rounds `finalPrice` only — the **only** place rounding happens |

**Higher priority = earlier execution.**

**Rounding principle:** All intermediate calculators work at full `DecimalNumber` precision. The `RoundingCalculator` rounds only `finalPrice` into an `ImmutableTaxablePrice` where both tax-excluded and tax-included are independently rounded. `originalPrice`, `unitPrice`, and `discountPrice` keep their full precision values.

### 4.5 Providers (Data Access Layer)

Providers are **pure data accessors** — they return raw database values with no computation. Calculators are responsible for computing derived values from the raw data.

```php
interface ProductProviderInterface
{
    /**
     * @throws ProductPriceNotFoundException when the product does not exist
     */
    public function getProductPriceData(int $productId, int $combinationId): ProductPriceData;
}
```

Returns a `ProductPriceData` DTO with 4 raw fields:
- `getPriceTaxExcluded()` — `ps_product.price`
- `getUnitPriceTaxExcluded()` — `ps_product.unit_price`
- `getCombinationImpactTaxExcluded()` — `ps_product_attribute.price` (0 when no combination)
- `getCombinationUnitPriceImpactTaxExcluded()` — `ps_product_attribute.unit_price_impact` (0 when no combination)

All getters are suffixed with `TaxExcluded` to prevent confusion about which tax context the values represent.

The `CatalogProductProvider` fetches product and combination data in a single query (LEFT JOIN when combinationId > 0). It throws `ProductPriceNotFoundException` instead of returning empty data when the product doesn't exist.

| Implementation               | Context | Source |
|------------------------------|---|---|
| `CatalogProductProvider`     | Cart (FO) | `ps_product` + `ps_product_attribute` |
| `OrderDetailProductProvider` | Order (BO) | `ps_order_detail` |
| `MockProductProvider`        | Tests | In-memory map keyed by productId or "productId-combinationId" |

---

## 5. Cart Pricing

### 5.1 CartPriceInterface

```php
interface CartPriceInterface
{
    public function getCartId(): int;

    public function getProductTotal(): TaxablePrice;
    public function setProductTotal(TaxablePrice $productTotal): void;

    public function getShippingTotal(): TaxablePrice;
    public function setShippingTotal(TaxablePrice $shippingTotal): void;

    public function getWrappingTotal(): TaxablePrice;
    public function setWrappingTotal(TaxablePrice $wrappingTotal): void;

    public function getDiscountTotal(): TaxablePrice;
    public function setDiscountTotal(TaxablePrice $discountTotal): void;

    public function getCartTotal(): TaxablePrice;
    public function setCartTotal(TaxablePrice $cartTotal): void;

    /** @return ProductPriceInterface[] */
    public function getProductPrices(): array;
    public function setProductPrices(array $productPrices): void;
}
```

Same pattern: `CartPrice` (lightweight) and `TrackedCartPrice` (debug with auto-tracking).

### 5.2 CartCalculatorInterface

```php
interface CartCalculatorInterface
{
    public function compute(CartPriceInterface $cartPrice): void;
}
```

Same principles: no `supports()`, PriceContext injected via constructor.

| Calculator | Priority | Responsibility |
|---|---|---|
| `ProductTotalCalculator` | 100 | Computes each product's price via `ProductCalculator`, sums into `productTotal` |
| `ShippingCalculator` | 80 | Computes shipping fees based on carrier, weight, cart rules |
| `WrappingCalculator` | 70 | Gift wrapping costs |
| `PercentageDiscountCalculator` | 64 | Percentage-based cart rule discounts |
| `AmountDiscountCalculator` | 63 | Fixed amount cart rule discounts |
| `FreeShippingDiscountCalculator` | 62 | Free shipping cart rules |
| `FreeGiftDiscountCalculator` | 61 | Free gift cart rules |
| `CartRoundingCalculator` | 10 | Final cart-level rounding (`PS_ROUND_TYPE` controls per-line vs per-total) |

### 5.3 CartManager

Orchestrates cart computation lifecycle: read -> check freshness -> compute -> persist.

```php
namespace PrestaShop\PrestaShop\Core\Pricing\Cart;

class CartManager
{
    public function __construct(
        protected readonly CartProductProviderInterface $cartProductProvider,
        protected readonly Checker\CompositeCartChecker $cartChecker,
        protected readonly Calculator\CartCalculator $cartCalculator,
        protected readonly CartPersisterInterface $cartPersister,
    ) {}

    public function getCartPrice(int $cartId): CartPriceInterface
    {
        $cartPrice = $this->cartProductProvider->getCartPrice($cartId);

        if (!$this->cartChecker->isUpToDate($cartPrice)) {
            $cartPrice = $this->cartCalculator->compute($cartPrice);
            $this->cartPersister->persist($cartPrice);
        }

        return $cartPrice;
    }

    public function invalidate(int $cartId): void;
    public function update(int $cartId, CartUpdate $update): CartPriceInterface;
}
```

### 5.4 CompositeCartChecker

Tagged `prestashop.pricing.cart_checker`. Detects staleness in: product prices, specific prices, tax rates, discount rules, shipping address, carrier selection.

```php
namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Checker;

class CompositeCartChecker
{
    /**
     * @param iterable<CartCheckerInterface> $checkers Injected via DI tagged_iterator
     */
    public function __construct(
        protected readonly iterable $checkers,
    ) {}

    public function isUpToDate(CartPrice $cartPrice): bool
    {
        foreach ($this->checkers as $checker) {
            if (!$checker->isUpToDate($cartPrice)) {
                return false; // Short-circuit on first stale check
            }
        }
        return true;
    }
}
```

---

## 6. Rounding Strategy

**Core principle: rounding happens ONCE, at the end of the pipeline.** All intermediate calculators operate at full `DecimalNumber` precision.

```php
interface RoundingServiceInterface
{
    public function round(DecimalNumber $value, ?int $precision = null): DecimalNumber;
}

class RoundingService implements RoundingServiceInterface
{
    public function __construct(int $legacyRoundMode = 0);
    public function round(DecimalNumber $value, ?int $precision = null): DecimalNumber;
}
```

The `RoundingService` maps `PS_PRICE_ROUND_MODE` config values (0-5) to `DecimalNumber` rounding modes (`ROUND_HALF_UP`, `ROUND_HALF_DOWN`, `ROUND_HALF_EVEN`, `ROUND_CEIL`, `ROUND_FLOOR`, `ROUND_TRUNCATE`).

**Phase 1 constraint:** Default precision is 0 (rounds to integers) to clearly prove the pipeline works. This will be replaced by currency precision in later phases.

The `RoundingCalculator` rounds only `finalPrice`, producing an `ImmutableTaxablePrice` where both tax-excluded and tax-included are independently rounded. This is necessary because rounding `taxExcluded` and letting `TaxablePrice` auto-derive `taxIncluded` would produce non-integer tax-included values when a tax rate is applied.

---

## 7. Debug Tooling

### 7.1 PricingRegistry

Request-scoped service that collects all `ProductPriceInterface` and `CartPriceInterface` instances computed during a request.

### 7.2 PricingHistoryDisplayer

Formats a `TrackedProductPrice`/`TrackedCartPrice` breakdown into:
- Human-readable string: `[BaseProductCalculator:42] unitPrice: 0 -> 29.99`
- Structured array for Twig rendering

Returns "No tracking data available" for non-tracked DTOs.

### 7.3 Symfony Debug Toolbar Integration

`PricingDataCollector` extends Symfony's `DataCollector`, reads from `PricingRegistry`, and renders in a dedicated profiler panel showing:
- Count of computed prices
- Per-product breakdown with modification history

---

## 8. Module Extensibility

### 8.1 Custom Calculators

Third-party modules add pricing rules by implementing `ProductCalculatorInterface` or `CartCalculatorInterface` and tagging their service:

```yaml
# In module's config/services.yml
services:
  MyModule\Pricing\LoyaltyDiscountCalculator:
    tags:
      - { name: 'prestashop.pricing.cart.product_calculator', priority: 65 }
```

### 8.2 Custom Cart Checkers

Modules can add their own staleness checks:

```yaml
services:
  MyModule\Pricing\LoyaltyPointsChecker:
    tags:
      - { name: 'prestashop.pricing.cart_checker' }
```

### 8.3 Replacing Providers

Modules can decorate data providers (they should not replace them completely):

```yaml
services:
  MyModule\Pricing\CustomProductProvider:
    decorates: PrestaShop\PrestaShop\Core\Pricing\Product\Provider\CatalogProductProvider
    arguments:
      $decorated: '@.inner'
```

---

## 9. Dependency Injection Configuration

### 9.1 Service Definition

File: `src/PrestaShopBundle/Resources/config/services/core/pricing.yml`

```yaml
services:
  _defaults:
    public: true
    autowire: true
    autoconfigure: true

  # Context (Phase 6)
  # PrestaShop\PrestaShop\Core\Pricing\Context\PriceContextFactory: ~
  # PrestaShop\PrestaShop\Core\Pricing\Context\PriceContext:
  #   lazy: true
  #   factory: ['@PrestaShop\PrestaShop\Core\Pricing\Context\PriceContextFactory', 'create']

  # Providers (cart context)
  PrestaShop\PrestaShop\Core\Pricing\Product\Provider\CatalogProductProvider:
    arguments:
      $dbPrefix: '%database_prefix%'
  # Providers (order context — Phase 5)
  # PrestaShop\PrestaShop\Core\Pricing\Product\Provider\OrderDetailProductProvider:
  #   arguments:
  #     $dbPrefix: '%database_prefix%'

  # Rounding
  PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingService:
    arguments:
      $legacyRoundMode: '@=service("prestashop.adapter.legacy.configuration").getInt("PS_PRICE_ROUND_MODE")'
  PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingServiceInterface:
    alias: PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingService

  # Calculators — cart context (separate tags per context)
  prestashop.pricing.cart.product.base_product_calculator:
    class: PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\BaseProductCalculator
    arguments:
      $productProvider: '@PrestaShop\PrestaShop\Core\Pricing\Product\Provider\CatalogProductProvider'
    tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 100 } ]

  # Calculators — order context (Phase 5)
  # prestashop.pricing.order.product.base_product_calculator:
  #   class: PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\BaseProductCalculator
  #   arguments:
  #     $productProvider: '@PrestaShop\PrestaShop\Core\Pricing\Product\Provider\OrderDetailProductProvider'
  #   tags: [ { name: 'prestashop.pricing.order.product_calculator', priority: 100 } ]

  # Calculators — the generic principle is to have multiple calculators, their execution order depends on their assigned priority
  # These will be added in Phase 6:
  # PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\SpecificPriceCalculator:
  #   tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 80 } ]
  # PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\GroupReductionCalculator:
  #   tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 70 } ]
  # PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\EcoTaxCalculator:
  #   tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 60 } ]
  # PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\TaxCalculator:
  #   tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 50 } ]
  # PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\CurrencyCalculator:
  #   tags: [ { name: 'prestashop.pricing.cart.product_calculator', priority: 30 } ]

  # Rounding calculator — shared across cart and order contexts
  PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\RoundingCalculator:
    tags:
      - { name: 'prestashop.pricing.cart.product_calculator', priority: 10 }
      - { name: 'prestashop.pricing.order.product_calculator', priority: 10 }

  # Cart calculator
  prestashop.pricing.cart.product_calculator:
    class: PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculator
    arguments:
      $calculators: !tagged_iterator { tag: 'prestashop.pricing.cart.product_calculator' }

  # Order calculator
  prestashop.pricing.order.product_calculator:
    class: PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculator
    arguments:
      $calculators: !tagged_iterator { tag: 'prestashop.pricing.order.product_calculator' }

  # Debug
  PrestaShop\PrestaShop\Core\Pricing\Debug\PricingRegistry: ~
  PrestaShop\PrestaShop\Core\Pricing\Debug\PricingHistoryDisplayer: ~
```

### 9.2 FO + BO Compatibility

The pricing services are defined in `src/PrestaShopBundle/Resources/config/services/core/common.yml` (imported by both FO and BO).

**FO challenge:** The FO `ContainerBuilder` supports `_instanceof` auto-tagging, `!tagged_iterator`, and compiler passes, but does NOT support PHP 8 attributes like `#[AutowireLocator]` or `#[TaggedLocator]`. Therefore:
- Use YAML-based tagging (not PHP 8 attributes) for pricing services
- Use `!tagged_iterator` (not `#[TaggedIterator]`) for injection
- If needed, add a `PricingCompilerPass` to handle complex service wiring

---

## 10. Feature Flag

### 10.1 Configuration

`new_pricing` is defined in `FeatureFlagSettings.php` and `feature_flag.xml`:

```php
public const FEATURE_FLAG_NEW_PRICING = 'new_pricing';
```

```xml
<feature_flag id="new_pricing" name="new_pricing" type="env,dotenv,db"
    label_wording="New pricing engine"
    label_domain="Admin.Advparameters.Feature"
    description_wording="Enable the new pricing computation engine. Warning: this is a development feature, prices will be incorrect until the implementation is complete."
    description_domain="Admin.Advparameters.Help"
    state="0" stability="beta" />
```

### 10.2 Integration Point

Inside `Product::getPriceStatic()`, the feature flag switches between legacy and new computation:

```php
// In Product::getPriceStatic() or a new ProductPriceFactory
if ($featureFlagManager->isEnabled(FeatureFlagSettings::FEATURE_FLAG_NEW_PRICING)) {
    $calculator = $container->get('prestashop.pricing.cart.product_calculator');
    // This may be the starting point where we create the TrackedProductPrice
    $productPrice = ProductPrice::create($id_product, $id_product_attribute, $quantity);
    $calculator->compute($productPrice);
    return (float) $productPrice->getFinalPrice()->getTaxIncluded()->toPrecision($decimals);
}
// ... legacy code continues
```

This allows gradual migration: the new system runs alongside the old one, and we can compare outputs.

---

## 11. Testing Strategy

### 11.1 Unit Tests (`tests/Unit/Core/Pricing/`)

Every value object, calculator, and debug service has dedicated unit tests using `MockProductProvider`:

```php
class BaseProductCalculatorTest extends TestCase
{
    public function testSetsBasePricesFromProvider(): void
    {
        $provider = new MockProductProvider([
            '1' => new ProductPriceData(
                new DecimalNumber('29.99'),
                new DecimalNumber('5.00'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
        ]);
        $calculator = new BaseProductCalculator($provider);
        $productPrice = ProductPrice::create(1, 0);
        $calculator->compute($productPrice);

        $this->assertTrue(
            $productPrice->getOriginalPrice()->getTaxExcluded()->equals(new DecimalNumber('29.99'))
        );
    }
}
```

Test files:

- `ValueObject/TaxRateTest.php` — construction, zero(), getMultiplier(), equals(), rejects negative
- `ValueObject/TaxablePriceTest.php` — fromTaxExcluded(), fromTaxIncluded(), zero(), setters auto-sync
- `ValueObject/ImmutableTaxablePriceTest.php` — stores values as-is, no auto-sync
- `ValueObject/PriceBreakdownTest.php` — addStep/getSteps/count
- `Product/ProductPriceTest.php` — create, quantity, getters/setters
- `Product/TrackedProductPriceTest.php` — setters record PriceModification, captures caller class
- `Product/Calculator/ProductCalculatorTest.php` — iterates calculators in order, empty pipeline
- `Product/Calculator/BaseProductCalculatorTest.php` — sets prices from provider, combination impacts, finalPrice initialization
- `Product/Calculator/RoundingCalculatorTest.php` — rounds finalPrice only, preserves other fields
- `Rounding/RoundingServiceTest.php` — all 6 rounding modes + custom precision
- `Debug/PricingRegistryTest.php` — register, retrieve, clear
- `Debug/PricingHistoryDisplayerTest.php` — format tracked vs non-tracked DTOs

### 11.2 Integration Tests (`tests/Integration/Core/Pricing/`)

- `Product/Provider/CatalogProductProviderTest.php` — queries real DB, verifies ProductPriceData, throws on missing product
- `PricingServiceWiringTest.php` — boots kernel, verifies all services exist and are correctly typed

### 11.3 Behat Tests (`tests/Integration/Behaviour/Features/Scenario/Pricing/`)

A dedicated `pricing` Behat suite (`behat.yml`) tests the full pipeline end-to-end using real products created via existing Behat steps:

- `product_price_calculation.feature` — simple product pricing, combination pricing
- Context: `PricingFeatureContext` — stateless steps that compute and assert in a single call, checking all 8 price values (tax excluded + tax included for original, unit, discount, final)

### 11.4 Comparison Tests (Transition Phase)

During migration, run both old and new systems and compare:

```php
class PricingComparisonTest extends TestCase
{
    /**
     * @dataProvider productPriceProvider
     */
    public function testNewPricingMatchesLegacy(int $productId, array $params): void
    {
        $legacyPrice = Product::getPriceStatic($productId, ...);
        $newPrice = $this->calculator->compute(...);

        // Initially: just verify the new system doesn't crash
        // Later: verify prices match
        // Finally: verify new prices are MORE accurate
    }
}
```

---

## 12. Implementation Phases

### Phase 1: Foundation (Issues #40948, #40949, #40951) ✅ PR #41039
- Feature flag `new_pricing`
- Exception hierarchy: `PricingException`, `InvalidTaxRateException`, `ProductPriceNotFoundException`
- `PricingConstants` (shared intermediate precision)
- Value objects: `TaxablePriceInterface`, `TaxablePrice`, `ImmutableTaxablePrice`, `TaxRate`, `PriceModification`, `PriceBreakdown`
- `ProductPriceInterface` + `ProductPrice` + `TrackedProductPrice` (with unitPrice, originalPrice, discountPrice, finalPrice, quantity)
- `ProductCalculatorInterface` + `ProductCalculator`
- `BaseProductCalculator` + `RoundingCalculator`
- `ProductProviderInterface` + `ProductPriceData` + `CatalogProductProvider` + `MockProductProvider`
- `RoundingServiceInterface` + `RoundingService`
- Debug tooling: `PricingRegistry`, `PricingHistoryDisplayer`
- DI configuration in `pricing.yml` + import in `common.yml`
- Unit tests, integration tests, Behat tests

### Phase 2: Cart Pricing initialization (Issues #41014, #40979)
- `CartPriceInterface` + `CartPrice` + `TrackedCartPrice`
- `CartCalculatorInterface` + `CartCalculator`
- Only `ProductTotalCartCalculator` at the beginning
- `CartManager` + `CompositeCartChecker`

### Phase 3: Integration Spike (Issue #40952)
- Plug calculator into `Product::getPriceStatic` behind feature flag
- Map all call sites, to define an exhaustive list of code to adapt

### Phase 4: Cart integration (FO side)
- Implement each touchpoint in dedicated issues based on the user stories
- Check that all touchpoints have been adapted

### Phase 5: Order integration (BO side)
- Order-context providers (read from `ps_order_detail`)
- Order-context calculator tags: `prestashop.pricing.order.product_calculator`
- Order editing, refund/credit slip computation

### Phase 6: Product and Cart Price Accuracy
- `PriceContext` + `PriceContextFactory` for more advanced calculators that need the context
- Remaining product calculators: SpecificPrice, GroupReduction, EcoTax, Tax, Currency
- Database providers for tax and specific price
- Remaining Cart calculators: Shipping, Wrapping, Discount (split into sub-calculators)
- Comparison tests against legacy system

### Phase 7: Validation
- Verify against ~50 known bugs
- Performance benchmarks
- Module compatibility testing
- Edge cases: multi-tax, B2B, ecotax, currency conversion, multistore

---

## 13. Key Files to Modify

| File | Change |
|---|---|
| `src/Core/FeatureFlag/FeatureFlagSettings.php` | Add `FEATURE_FLAG_NEW_PRICING` constant |
| `install-dev/data/xml/feature_flag.xml` | Add `new_pricing` flag entry |
| `src/PrestaShopBundle/Resources/config/services/core/common.yml` | Import `pricing.yml` |
| `src/PrestaShopBundle/Resources/config/services/core/pricing.yml` | All pricing DI |
| `classes/Product.php` (`getPriceStatic`) | Feature flag branch to new calculator |
| `classes/Cart.php` (`getOrderTotal`) | Feature flag branch to new cart calculator |
| `src/Adapter/ContainerBuilder.php` | May need CompilerPass for FO pricing |
| `src/PrestaShopBundle/PrestaShopBundle.php` | Register `PricingCompilerPass` if needed |

---

## 14. Resolved Design Decisions

1. **Mutable DTOs:** `ProductPrice`/`CartPrice` use setters, not immutable `withX()` methods. Simpler calculator code.
2. **Debug-only tracking:** `TrackedProductPrice` auto-records via `debug_backtrace()`. Calculators never interact with the audit trail.
3. **PriceContext as service:** Injected via constructor DI, not passed as method parameter.
4. **No `supports()` method:** Calculators return early from `compute()` when not relevant.
5. **Separate tags per context:** `prestashop.pricing.cart.product_calculator` / `prestashop.pricing.order.product_calculator` for clean FO/BO separation.
6. **Currency conversion** as a calculator step (priority 30), not a separate system.
7. **Discount calculators split** into smaller focused services (percentage, amount, free shipping, free gift).
8. **No `final` classes:** All classes use `protected` to allow extension by modules.
9. **Protected constructor on TaxablePrice:** Forces use of `fromTaxExcluded()` / `fromTaxIncluded()` factories for clarity.
10. **No arithmetic on TaxablePrice:** No plus/minus/times/dividedBy — calculators set new values via ProductPrice setters to ensure debug tracking works.
11. **ImmutableTaxablePrice for finalPrice:** Independently rounded tax-excluded and tax-included values that won't be recomputed by auto-sync.
12. **Provider returns raw data:** `ProductProviderInterface` returns a `ProductPriceData` DTO with raw DB values. Computation (e.g. adding combination impact) is done by the calculator.
13. **Provider throws on missing product:** `ProductPriceNotFoundException` instead of returning zeros.
14. **taxAmount = taxIncluded - taxExcluded:** Consistent across all code paths, avoids precision drift from re-deriving via rate/100.
15. **ProductCalculator implements ProductCalculatorInterface:** Named `ProductCalculator` (not orchestrator) because callers simply want to calculate a price — the pipeline is an implementation detail.

## 15. Open Questions / Discussion Points

1. **Caching strategy:** The current system caches aggressively in static properties. Should the new system use PSR-6 cache for computed prices, or rely on the `CartChecker` freshness mechanism only?
2. **Backward compatibility for hooks:** The legacy system has hooks like `actionProductPriceCalculation`. Should the new calculators dispatch equivalent Symfony events, or should this be handled at the integration point in `getPriceStatic`?
3. **Multistore:** How should the pricing pipeline handle multistore? Should the `PriceContext` carry shop context, or should different shops get different service configurations entirely?
