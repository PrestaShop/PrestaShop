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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductUpdatablePropertyFillerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;

class UpdateProductHandler
{
    /**
     * @var ProductUpdatablePropertyFillerInterface
     */
    private $productUpdatablePropertyFiller;

    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    /**
     * @param ProductUpdatablePropertyFillerInterface $productUpdatablePropertyFiller
     * @param ProductMultiShopRepository $productRepository
     * @param ProductIndexationUpdater $productIndexationUpdater
     */
    public function __construct(
        ProductUpdatablePropertyFillerInterface $productUpdatablePropertyFiller,
        ProductMultiShopRepository $productRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->productUpdatablePropertyFiller = $productUpdatablePropertyFiller;
        $this->productRepository = $productRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    /**
     * @param UpdateProductCommand $command
     */
    public function handle(UpdateProductCommand $command): void
    {
        $shopConstraint = $command->getShopConstraint();
        $product = $this->productRepository->getByShopConstraint($command->getProductId(), $shopConstraint);
        $wasVisibleOnSearch = $this->productIndexationUpdater->isVisibleOnSearch($product);

        $updatableProperties = $this->productUpdatablePropertyFiller->fillUpdatableProperties(
            $product,
            $command
        );

        if (empty($updatableProperties)) {
            return;
        }

        $this->productRepository->partialUpdate(
            $product,
            $updatableProperties,
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_PRODUCT
        );

        $isVisibleOnSearch = $this->productIndexationUpdater->isVisibleOnSearch($product);
        if ($wasVisibleOnSearch !== $isVisibleOnSearch) {
            $this->productIndexationUpdater->updateIndexation($product);
        }
    }
}
