<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Provides data for the product shop selection form, mostly which shops are associated to the product
 * and which one is the current selected shop.
 */
class ProductShopsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var int|null
     */
    private $contextShopId;

    /**
     * @param ProductRepository $productRepository
     * @param int|null $contextShopId
     */
    public function __construct(
        ProductRepository $productRepository,
        ?int $contextShopId
    ) {
        $this->productRepository = $productRepository;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        $associatedShopIds = $this->productRepository->getAssociatedShopIds(new ProductId($id));
        $selectedShops = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $associatedShopIds);

        return [
            'source_shop_id' => $this->contextShopId,
            'initial_shops' => $selectedShops,
            'selected_shops' => $selectedShops,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData()
    {
        // This form is not used for creation only update anyway
        return [];
    }
}
