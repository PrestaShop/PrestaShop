<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilderInterface;

/**
 * Handles data posted from combination form
 */
class CombinationFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var CombinationCommandsBuilderInterface
     */
    private $commandsBuilder;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * @param CommandBusInterface $bus
     * @param CombinationCommandsBuilderInterface $commandsBuilder
     */
    public function __construct(
        CommandBusInterface $bus,
        CombinationCommandsBuilderInterface $commandsBuilder,
        int $contextShopId,
        int $defaultShopId
    ) {
        $this->bus = $bus;
        $this->commandsBuilder = $commandsBuilder;
        $this->contextShopId = $contextShopId;
        $this->defaultShopId = $defaultShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        // Not used for creation
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $singleShopConstraint = $this->contextShopId ? ShopConstraint::shop($this->contextShopId) : ShopConstraint::shop($this->defaultShopId);

        $commands = $this->commandsBuilder->buildCommands(new CombinationId($id), $data, $singleShopConstraint);

        foreach ($commands as $command) {
            $this->bus->handle($command);
        }
    }
}
