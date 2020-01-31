<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
use PrestaShop\PrestaShop\Core\Domain\Order\Command\CancelOrderProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;

/**
 * Class CancellationFormDataHandler
 */
final class CancellationFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $commandBus
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $commandBus, CommandBusInterface $queryBus)
    {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $orderForViewing = $this->queryBus->handle(new GetOrderForViewing($id));

        $toBeCanceledProducts = [];
        foreach ($data['products'] as $product) {
            if ($data['selected_' . $product->getOrderDetailId()]) {
                $toBeCanceledProducts[$product->getOrderDetailId()] = $data['quantity_' . $product->getOrderDetailId()];
            }
        }

        $command = new CancelOrderProductCommand(
            $data['products'],
            $toBeCanceledProducts,
            $orderForViewing
        );

        $this->commandBus->handle($command);
    }
}
