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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter\BulkCombinationFormDataFormatter;

class BulkCombinationFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var BulkCombinationFormDataFormatter
     */
    private $bulkCombinationFormDataFormatter;

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
     * @param CommandBusInterface $commandBus
     * @param BulkCombinationFormDataFormatter $bulkCombinationFormDataFormatter
     * @param CombinationCommandsBuilderInterface $commandsBuilder
     * @param int $contextShopId
     * @param int $defaultShopId
     */
    public function __construct(
        CommandBusInterface $commandBus,
        BulkCombinationFormDataFormatter $bulkCombinationFormDataFormatter,
        CombinationCommandsBuilderInterface $commandsBuilder,
        int $contextShopId,
        int $defaultShopId
    ) {
        $this->commandBus = $commandBus;
        $this->commandsBuilder = $commandsBuilder;
        $this->bulkCombinationFormDataFormatter = $bulkCombinationFormDataFormatter;
        $this->contextShopId = $contextShopId;
        $this->defaultShopId = $defaultShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        // not used for creation
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data): void
    {
        // @todo: a hook system should be integrated in this handler for extendability
        $singleShopConstraint = $this->contextShopId ? ShopConstraint::shop($this->contextShopId) : ShopConstraint::shop($this->defaultShopId);
        $formattedData = $this->bulkCombinationFormDataFormatter->format($data);
        $commands = $this->commandsBuilder->buildCommands(new CombinationId($id), $formattedData, $singleShopConstraint);

        foreach ($commands as $command) {
            $this->commandBus->handle($command);
        }
    }
}
