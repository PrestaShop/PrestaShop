<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update\Filler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;

/**
 * Fills product properties which are related to product price
 */
class PricesFiller implements ProductFillerInterface
{
    /**
     * @var NumberExtractor
     */
    private $numberExtractor;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param NumberExtractor $numberExtractor
     * @param Configuration $configuration
     */
    public function __construct(
        NumberExtractor $numberExtractor,
        Configuration $configuration
    ) {
        $this->numberExtractor = $numberExtractor;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Product $product, UpdateProductCommand $command): array
    {
        $updatableProperties = $this->fillWithPrices(
            $product,
            $command->getPrice(),
            $command->getUnitPrice(),
            $command->getWholesalePrice(),
            $command->getEcotax(),
            $command->getShopConstraint()
        );

        if (null !== $command->getUnity()) {
            $product->unity = $command->getUnity();
            $updatableProperties[] = 'unity';
        }

        $taxRulesGroupId = $command->getTaxRulesGroupId();

        if (null !== $taxRulesGroupId) {
            $product->id_tax_rules_group = $taxRulesGroupId;
            $updatableProperties[] = 'id_tax_rules_group';
        }

        if (null !== $command->isOnSale()) {
            $product->on_sale = $command->isOnSale();
            $updatableProperties[] = 'on_sale';
        }

        return $updatableProperties;
    }

    /**
     * Wraps following properties filling: price, unit_price, unit_price_ratio, wholesale_price
     * as most of them (price, unit_price, unit_price_ratio) are highly coupled & depends on each other
     *
     * @param Product $product
     * @param DecimalNumber|null $price
     * @param DecimalNumber|null $unitPrice
     * @param DecimalNumber|null $wholesalePrice
     * @param ShopConstraint $shopConstraint
     *
     * @return string[] updatable properties
     */
    private function fillWithPrices(
        Product $product,
        ?DecimalNumber $price,
        ?DecimalNumber $unitPrice,
        ?DecimalNumber $wholesalePrice,
        ?DecimalNumber $ecotax,
        ShopConstraint $shopConstraint
    ): array {
        $updatableProperties = [];
        if (null !== $wholesalePrice) {
            $product->wholesale_price = (float) (string) $wholesalePrice;
            $updatableProperties[] = 'wholesale_price';
        }

        if (null !== $price) {
            $product->price = (float) (string) $price;
            $updatableProperties[] = 'price';
        }

        if (null !== $ecotax) {
            $product->ecotax = (float) (string) $ecotax;
            $updatableProperties[] = 'ecotax';
        }

        // When product price is zero we force unit price to zero
        $productPrice = $this->getProductFinalPrice($price, $ecotax, $product, $shopConstraint);
        $currentUnitPrice = $unitPrice ?: $this->numberExtractor->extract($product, 'unit_price');
        if ($productPrice->equalsZero() && !$currentUnitPrice->equalsZero()) {
            $unitPrice = new DecimalNumber('0');
        }

        if (null !== $unitPrice) {
            $product->unit_price = (float) (string) $unitPrice;
            $updatableProperties[] = 'unit_price';
        }

        // When price or unit price is changed the ratio must be updated, but only the object field
        // we don't ask to update this property since it will be updated via an SQL query by the Product class
        if (null !== $unitPrice || null !== $price) {
            $this->fillUnitPriceRatio($product, $price, $unitPrice);
        }

        return $updatableProperties;
    }

    /**
     * @param DecimalNumber|null $price
     * @param DecimalNumber|null $ecotax
     * @param Product $product
     * @param ShopConstraint $shopConstraint
     *
     * @return DecimalNumber
     */
    private function getProductFinalPrice(
        ?DecimalNumber $price,
        ?DecimalNumber $ecotax,
        Product $product,
        ShopConstraint $shopConstraint
    ): DecimalNumber {
        $price = $price ?: $this->numberExtractor->extract($product, 'price');

        $ecotaxEnabled = (bool) $this->configuration->get('PS_USE_ECOTAX', null, $shopConstraint);
        if ($ecotaxEnabled) {
            $ecotax = $ecotax ?: $this->numberExtractor->extract($product, 'ecotax');
        } else {
            $ecotax = new DecimalNumber('0');
        }

        return $price->plus($ecotax);
    }

    /**
     * @param Product $product
     * @param DecimalNumber|null $price
     * @param DecimalNumber|null $unitPrice
     */
    private function fillUnitPriceRatio(Product $product, ?DecimalNumber $price, ?DecimalNumber $unitPrice): void
    {
        $price = $price ?: $this->numberExtractor->extract($product, 'price');
        $unitPrice = $unitPrice ?: $this->numberExtractor->extract($product, 'unit_price');

        // Reminder: regardless of what we compute here a final update is also performed in Product::updateUnitRatio
        // this part is more destined to keep the field consistent in the $product object
        $this->setUnitPriceRatio($product, $price, $unitPrice);
    }

    /**
     * @param Product $product
     * @param DecimalNumber $price
     * @param DecimalNumber $unitPrice
     */
    private function setUnitPriceRatio(Product $product, DecimalNumber $price, DecimalNumber $unitPrice): void
    {
        // If unit price or price is zero, then reset ratio to zero too
        if ($unitPrice->equalsZero() || $price->equalsZero()) {
            $ratio = new DecimalNumber('0');
        } else {
            $ratio = $price->dividedBy($unitPrice);
        }

        // Ratio is computed based on price and unit price, we update it so that the value is up-to-date in hooks
        $product->unit_price_ratio = (float) (string) $ratio;
    }
}
