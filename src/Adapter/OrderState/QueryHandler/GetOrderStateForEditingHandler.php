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

namespace PrestaShop\PrestaShop\Adapter\OrderState\QueryHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryHandler\GetOrderStateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult\EditableOrderState;
use SplFileInfo;

/**
 * Handles command that gets orderState for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetOrderStateForEditingHandler implements GetOrderStateForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderStateForEditing $query)
    {
        $orderStateId = $query->getOrderStateId();
        $orderState = new OrderState($orderStateId->getValue());

        if ($orderState->id !== $orderStateId->getValue()) {
            throw new OrderStateNotFoundException($orderStateId, sprintf('OrderState with id "%s" was not found', $orderStateId->getValue()));
        }

        $filePath = _PS_ORDER_STATE_IMG_DIR_ . $orderState->id . '.gif';
        $file = file_exists($filePath) ? new SplFileInfo($filePath) : null;

        return new EditableOrderState(
            $orderStateId,
            $orderState->name,
            $file,
            $orderState->color,
            (bool) $orderState->logable,
            (bool) $orderState->invoice,
            (bool) $orderState->hidden,
            (bool) $orderState->send_email,
            (bool) $orderState->pdf_invoice,
            (bool) $orderState->pdf_delivery,
            (bool) $orderState->shipped,
            (bool) $orderState->paid,
            (bool) $orderState->delivery,
            $orderState->template,
            (bool) $orderState->deleted
        );
    }
}
