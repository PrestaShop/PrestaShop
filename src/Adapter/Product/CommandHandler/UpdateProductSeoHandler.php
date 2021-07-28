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
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSeoPropertiesFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductSeoHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use Product;

/**
 * Handles @var UpdateProductSeoCommand using legacy object model
 */
class UpdateProductSeoHandler implements UpdateProductSeoHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductSeoPropertiesFiller
     */
    private $productSeoPropertiesFiller;

    /**
     * @param ProductRepository $productRepository
     * @param ProductSeoPropertiesFiller $productSeoPropertiesFiller
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductSeoPropertiesFiller $productSeoPropertiesFiller
    ) {
        $this->productRepository = $productRepository;
        $this->productSeoPropertiesFiller = $productSeoPropertiesFiller;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSeoCommand $command): void
    {
        $product = $this->productRepository->get($command->getProductId());
        $updatableProperties = $this->fillUpdatableProperties($product, $command);

        $this->productRepository->partialUpdate($product, $updatableProperties, CannotUpdateProductException::FAILED_UPDATE_SEO);
    }

    /**
     * @param Product $product
     * @param UpdateProductSeoCommand $command
     *
     * @return array
     */
    private function fillUpdatableProperties(Product $product, UpdateProductSeoCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getRedirectOption()) {
            $updatableProperties = array_merge(
                $updatableProperties,
                $this->productSeoPropertiesFiller->fillWithRedirectOption($product, $command->getRedirectOption())
            );
        }

        $localizedMetaDescriptions = $command->getLocalizedMetaDescriptions();
        if (null !== $localizedMetaDescriptions) {
            $product->meta_description = $localizedMetaDescriptions;
            $updatableProperties['meta_description'] = array_keys($localizedMetaDescriptions);
        }

        $localizedMetaTitles = $command->getLocalizedMetaTitles();
        if (null !== $localizedMetaTitles) {
            $product->meta_title = $localizedMetaTitles;
            $updatableProperties['meta_title'] = array_keys($localizedMetaTitles);
        }

        $localizedLinkRewrites = $command->getLocalizedLinkRewrites();
        if (null !== $localizedLinkRewrites) {
            $product->link_rewrite = $localizedLinkRewrites;
            $updatableProperties['link_rewrite'] = array_keys($localizedLinkRewrites);
        }

        return $updatableProperties;
    }
}
