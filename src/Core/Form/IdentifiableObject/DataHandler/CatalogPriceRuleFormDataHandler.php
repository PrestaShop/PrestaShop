<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    private $isSingleShopContext;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param CommandBusInterface $commandBus
     * @param $isSingleShopContext
     * @param $contextShopId
     */
    public function __construct(
        CommandBusInterface $commandBus,
        $isSingleShopContext,
        $contextShopId
    ) {
        $this->commandBus = $commandBus;
        $this->contextShopId = $contextShopId;
        $this->isSingleShopContext = $isSingleShopContext;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if ($this->isSingleShopContext && !isset($data['id_shop'])) {
            $data['id_shop'] = $this->contextShopId;
        }

        if ($data['leave_initial_price']) {
            $data['price'] = -1;
        }

        /** @var CatalogPriceRuleId $catalogPriceRuleId */
        $catalogPriceRuleId = $this->commandBus->handle(new AddCatalogPriceRuleCommand(
            $data['name'],
            (int) $data['id_currency'],
            (int) $data['id_country'],
            (int) $data['id_group'],
            (int) $data['from_quantity'],
            (float) $data['reduction'],
            (int) $data['id_shop'],
            (float) $data['price'],
            $data['from'] ?: date('Y-m-d H:i:s'),
            $data['to'] ?: date('Y-m-d H:i:s'),
            $data['reduction_type'],
            (bool) $data['include_tax']
        ));

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
        $command->setName($data['name']);
        $command->setShopId((int) $data['id_shop']);
        $command->setCurrencyId((int) $data['id_currency']);
        $command->setCountryId((int) $data['id_country']);
        $command->setGroupId((int) $data['id_group']);
        $command->setFromQuantity((int) $data['from_quantity']);
        $command->setPrice((float) $data['price']);
        $command->setFrom($data['from']);
        $command->setTo($data['to']);
        $command->setReductionType($data['reduction_type']);
        $command->setIncludeTax((bool) $data['include_tax']);
        $command->setReduction((float) $data['reduction']);
    }
}
