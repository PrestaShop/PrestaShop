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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\DeleteProductFromShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\CommandHandler\DeleteProductFromShopsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Handles @see DeleteProductFromShopsCommand using dedicated service
 */
class DeleteProductFromShopsHandler implements DeleteProductFromShopsHandlerInterface
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var CombinationMultiShopRepository
     */
    private $combinationMultiShopRepository;

    /**
     * @param ProductMultiShopRepository $productRepository
     */
    public function __construct(
        ProductMultiShopRepository $productRepository,
        CombinationMultiShopRepository $combinationMultiShopRepository
    ) {
        $this->productRepository = $productRepository;
        $this->combinationMultiShopRepository = $combinationMultiShopRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(DeleteProductFromShopsCommand $command): void
    {
        $shopIds = $command->getShopIds();
        $productId = $command->getProductId();

        if ($this->productRepository->hasCombinations($command->getProductId())) {
            foreach ($shopIds as $shopId) {
                $this->combinationMultiShopRepository->deleteByProductId($productId, ShopConstraint::shop($shopId->getValue()));
            }
        }

        $this->productRepository->deleteFromShops($command->getProductId(), $shopIds);
    }
}
