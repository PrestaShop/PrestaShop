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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductPropertiesFillerProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;

class UpdateProductHandler
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductPropertiesFillerProvider
     */
    private $productPropertiesFillerProvider;

    public function __construct(
        ProductMultiShopRepository $productRepository,
        ProductPropertiesFillerProvider $productPropertiesFillerProvider
    ) {
        $this->productRepository = $productRepository;
        $this->productPropertiesFillerProvider = $productPropertiesFillerProvider;
    }

    public function handle(UpdateProductCommand $command): void
    {
        $shopConstraint = $command->getShopConstraint();
        $product = $this->productRepository->getByShopConstraint($command->getProductId(), $shopConstraint);

        $dtoList = [
            $command->getOptions(),
            $command->getBasicInformation(),
        ];

        $updatableProperties = [];

        foreach ($dtoList as $dto) {
            $filler = $this->productPropertiesFillerProvider->getFiller($dto);
            $updatableProperties = array_merge($updatableProperties, $filler->fillUpdatableProperties($product, $dto));
        }

        // @todo: other commands in dedicated PR's

        $this->productRepository->partialUpdate(
            $product,
            $updatableProperties,
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_OPTIONS
        );
    }
}
