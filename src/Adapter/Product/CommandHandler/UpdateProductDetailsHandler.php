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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductDetailsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use Product;

/**
 * Handles @see UpdateProductDetailsCommand using leagacy object model
 */
final class UpdateProductDetailsHandler implements UpdateProductDetailsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductDetailsCommand $command): void
    {
        $product = $this->productRepository->get($command->getProductId());
        $updatableProperties = $this->fillUpdatableProperties($product, $command);

        $this->productRepository->partialUpdate($product, $updatableProperties, CannotUpdateProductException::FAILED_UPDATE_DETAILS);
    }

    /**
     * @param Product $product
     * @param UpdateProductDetailsCommand $command
     *
     * @return string[] updatable properties
     */
    private function fillUpdatableProperties(Product $product, UpdateProductDetailsCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getEan13()) {
            $product->ean13 = $command->getEan13()->getValue();
            $updatableProperties[] = 'ean13';
        }

        if (null !== $command->getIsbn()) {
            $product->isbn = $command->getIsbn()->getValue();
            $updatableProperties[] = 'isbn';
        }

        if (null !== $command->getMpn()) {
            $product->mpn = $command->getMpn();
            $updatableProperties[] = 'mpn';
        }

        if (null !== $command->getReference()) {
            $product->reference = $command->getReference()->getValue();
            $updatableProperties[] = 'reference';
        }

        if (null !== $command->getUpc()) {
            $product->upc = $command->getUpc()->getValue();
            $updatableProperties[] = 'upc';
        }

        return $updatableProperties;
    }
}
