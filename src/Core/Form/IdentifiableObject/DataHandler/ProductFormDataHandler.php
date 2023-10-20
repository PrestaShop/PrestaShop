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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilderInterface;

/**
 * Handles data posted from product form
 */
class ProductFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var ProductCommandsBuilderInterface
     */
    private $commandsBuilder;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * @var int|null
     */
    private $contextShopId;

    /**
     * @param CommandBusInterface $bus
     * @param ProductCommandsBuilderInterface $commandsBuilder
     * @param int $defaultShopId
     * @param int|null $contextShopId
     */
    public function __construct(
        CommandBusInterface $bus,
        ProductCommandsBuilderInterface $commandsBuilder,
        int $defaultShopId,
        ?int $contextShopId
    ) {
        $this->bus = $bus;
        $this->commandsBuilder = $commandsBuilder;
        $this->defaultShopId = $defaultShopId;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        // If a shop is selected in the context the product is added to it, if not use the default shop as a fallback
        $createCommand = new AddProductCommand(
            $data['type'],
            (int) $data['shop_id']
        );

        /** @var ProductId $productId */
        $productId = $this->bus->handle($createCommand);

        return $productId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $shopConstraint = null !== $this->contextShopId ? ShopConstraint::shop($this->contextShopId) : ShopConstraint::shop($this->defaultShopId);
        $commands = $this->commandsBuilder->buildCommands(
            new ProductId($id),
            $data,
            $shopConstraint
        );

        foreach ($commands as $command) {
            $this->bus->handle($command);
        }
    }
}
