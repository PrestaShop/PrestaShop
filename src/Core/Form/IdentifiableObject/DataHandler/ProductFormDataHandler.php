<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
