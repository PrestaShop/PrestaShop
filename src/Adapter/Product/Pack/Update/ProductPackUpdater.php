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

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\Update;

use Pack;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

/**
 * Provides methods related to Product Pack update
 */
class ProductPackUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductPackRepository
     */
    private $productPackRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ProductPackRepository $productPackRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductPackRepository $productPackRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productPackRepository = $productPackRepository;
    }

    /**
     * @param PackId $packId
     * @param QuantifiedProduct[] $productsForPacking
     *
     * @throws CoreException
     * @throws ProductPackConstraintException
     * @throws ProductPackException
     */
    public function setPackProducts(PackId $packId, array $productsForPacking): void
    {
        $pack = $this->productRepository->getProductByDefaultShop($packId);
        if ($pack->product_type !== ProductType::TYPE_PACK) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_PACK_TYPE);
        }

        // validate if provided products are available for packing before emptying the pack
        foreach ($productsForPacking as $productForPacking) {
            $this->assertProductIsAvailableForPacking($productForPacking->getProductId()->getValue());
        }

        $this->productPackRepository->removeAllProductsFromPack($packId);

        //reset cache_default_attribute
        $pack->setDefaultAttribute(CombinationId::NO_COMBINATION);

        try {
            foreach ($productsForPacking as $productForPacking) {
                $this->productPackRepository->addProductToPack($packId, $productForPacking);
            }
        } finally {
            Pack::resetStaticCache();
        }
    }

    /**
     * @param int $productId
     *
     * @throws CoreException
     * @throws ProductPackConstraintException
     */
    private function assertProductIsAvailableForPacking(int $productId): void
    {
        try {
            if (Pack::isPack($productId)) {
                throw new ProductPackConstraintException(
                    sprintf('Product #%d is a pack itself. It cannot be packed', $productId),
                    ProductPackConstraintException::CANNOT_ADD_PACK_INTO_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when asserting if product #%d is pack', $productId),
                0,
                $e
            );
        }
    }
}
