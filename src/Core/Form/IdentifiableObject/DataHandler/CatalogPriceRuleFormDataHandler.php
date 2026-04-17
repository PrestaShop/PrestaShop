<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\AddCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command\EditCatalogPriceRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;

/**
 * Handles submitted catalog price rule form data
 */
final class CatalogPriceRuleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var bool
     */
    private $isMultishopEnabled;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param CommandBusInterface $commandBus
     * @param bool $isMultishopEnabled
     * @param int $contextShopId
     */
    public function __construct(
        CommandBusInterface $commandBus,
        bool $isMultishopEnabled,
        int $contextShopId
    ) {
        $this->commandBus = $commandBus;
        $this->contextShopId = $contextShopId;
        $this->isMultishopEnabled = $isMultishopEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): int
    {
        if (!$this->isMultishopEnabled) {
            $data['id_shop'] = $this->contextShopId;
        }

        if ($data['leave_initial_price']) {
            $data['price'] = -1;
        }

        $command = new AddCatalogPriceRuleCommand(
            $data['name'],
            (int) $data['id_currency'],
            (int) $data['id_country'],
            (int) $data['id_group'],
            (int) $data['from_quantity'],
            $data['reduction']['type'],
            (string) $data['reduction']['value'],
            (int) $data['id_shop'],
            (bool) $data['reduction']['include_tax'],
            (float) $data['price']
        );

        if ($data['date_range']['from']) {
            $command->setDateTimeFrom($data['date_range']['from']);
        }

        if ($data['date_range']['to']) {
            $command->setDateTimeTo($data['date_range']['to']);
        }

        /** @var CatalogPriceRuleId $catalogPriceRuleId */
        $catalogPriceRuleId = $this->commandBus->handle($command);

        return $catalogPriceRuleId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws CatalogPriceRuleException
     */
    public function update($catalogPriceRuleId, array $data)
    {
        $editCatalogPriceRuleCommand = new EditCatalogPriceRuleCommand((int) $catalogPriceRuleId);
        $this->fillCommandWithData($editCatalogPriceRuleCommand, $data);

        $this->commandBus->handle($editCatalogPriceRuleCommand);
    }

    /**
     * @param EditCatalogPriceRuleCommand $command
     * @param array $data
     *
     * @throws CatalogPriceRuleException
     */
    private function fillCommandWithData(EditCatalogPriceRuleCommand $command, array $data)
    {
        if ($data['leave_initial_price']) {
            $data['price'] = -1;
        }

        $command->setName($data['name']);
        $command->setShopId((int) $data['id_shop']);
        $command->setCurrencyId((int) $data['id_currency']);
        $command->setCountryId((int) $data['id_country']);
        $command->setGroupId((int) $data['id_group']);
        $command->setFromQuantity((int) $data['from_quantity']);
        $command->setPrice((float) $data['price']);
        $command->setIncludeTax((bool) $data['reduction']['include_tax']);
        $command->setReduction($data['reduction']['type'], (string) $data['reduction']['value']);

        if ($data['date_range']['from']) {
            $command->setDateTimeFrom($data['date_range']['from']);
        }

        if ($data['date_range']['to']) {
            $command->setDateTimeTo($data['date_range']['to']);
        }
    }
}
