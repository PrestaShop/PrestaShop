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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use OrderInvoice;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\UpdateInvoiceNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\CommandHandler\UpdateInvoiceNoteHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Exception\InvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Exception\InvoiceNotFoundException;
use Validate;

/**
 * @internal
 */
final class UpdateInvoiceNoteHandler implements UpdateInvoiceNoteHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateInvoiceNoteCommand $command): void
    {
        $note = $command->getNote();
        $orderInvoice = new OrderInvoice($command->getOrderInvoiceId()->getValue());

        if (!Validate::isLoadedObject($orderInvoice) && Validate::isCleanHtml($note)) {
            throw new InvoiceNotFoundException(sprintf('Order invoice with id "%d" was not found', $command->getOrderInvoiceId()->getValue()));
        }

        $orderInvoice->note = $note;

        if (!$orderInvoice->save()) {
            throw new InvoiceException('The invoice note was not saved.');
        }
    }
}
