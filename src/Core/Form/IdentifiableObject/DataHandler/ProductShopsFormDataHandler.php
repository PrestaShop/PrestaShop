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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\CopyProductToShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\DeleteProductFromShopsCommand;
use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Entity\Shop;

class ProductShopsFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @param CommandBusInterface $bus
     * @param ShopRepository $shopRepository
     */
    public function __construct(
        CommandBusInterface $bus,
        ShopRepository $shopRepository
    ) {
        $this->bus = $bus;
        $this->shopRepository = $shopRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        // The form is only used for update not creation
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        $productId = (int) $id;
        $allShops = $this->shopRepository->findAll();

        $sourceShopId = (int) $data['source_shop_id'];
        $selectedShops = $data['selected_shops'];
        $initialShops = array_map(static function (string $shopId): int {
            return (int) $shopId;
        }, $data['initial_shops']);

        $shopsToRemove = [];
        $shopsToCopy = [];

        /** @var Shop $shop */
        foreach ($allShops as $shop) {
            if (in_array($shop->getId(), $initialShops) && !in_array($shop->getId(), $selectedShops)) {
                $shopsToRemove[] = $shop->getId();
            } elseif (!in_array($shop->getId(), $initialShops) && in_array($shop->getId(), $selectedShops)) {
                $shopsToCopy[] = $shop->getId();
            }
        }

        // Remove non associated shops
        if (!empty($shopsToRemove)) {
            $this->bus->handle(new DeleteProductFromShopsCommand(
                $productId,
                $shopsToRemove
            ));
        }

        // Copy data from source targets
        foreach ($shopsToCopy as $targetShop) {
            $this->bus->handle(new CopyProductToShopCommand(
                $productId,
                $sourceShopId,
                (int) $targetShop
            ));
        }
    }
}
