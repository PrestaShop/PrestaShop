<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormIntrospectionOptionsProviderInterface;

/**
 * Lets FormFieldTreeProvider introspect the product edit form — the most commonly targeted form
 * of the BO — despite its required options. The values only need to make the form BUILD, but the
 * product id must point at a REAL product: FooterType generates the preview/FO links at build
 * time and the legacy Link class rejects a product it cannot link to. The standard product type
 * exposes the common field set (combination-specific subforms excluded by design), the shop
 * comes from the context. On a catalog with no product at all the build fails and the form
 * gracefully reads as not introspectable.
 */
final class ProductFormIntrospectionOptionsProvider implements FormIntrospectionOptionsProviderInterface
{
    public function __construct(
        private readonly ShopContext $shopContext,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function supports(string $formId, string $formTypeClass): bool
    {
        return is_a($formTypeClass, EditProductFormType::class, true);
    }

    public function getOptions(string $formId, string $formTypeClass): array
    {
        return [
            'product_id' => (int) $this->connection->fetchOne(
                'SELECT MIN(id_product) FROM ' . $this->dbPrefix . 'product'
            ),
            'shop_id' => $this->shopContext->getId(),
            'product_type' => ProductType::TYPE_STANDARD,
            'tax_rules_group_id' => 0,
        ];
    }
}
