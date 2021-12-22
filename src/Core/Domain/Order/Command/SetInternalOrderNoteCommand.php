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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Sets internal note about order that can only be seen in Back Office.
 */
class SetInternalOrderNoteCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var string
     */
    private $internalNote;

    /**
     * @param int $orderId
     * @param string $internalNote
     */
    public function __construct($orderId, $internalNote)
    {
        $this->assertInternalNoteIsString($internalNote);

        $this->orderId = new OrderId($orderId);
        $this->internalNote = $internalNote;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getInternalNote()
    {
        return $this->internalNote;
    }

    /**
     * @param string $internalNote
     *
     * @throws OrderConstraintException
     */
    private function assertInternalNoteIsString($internalNote)
    {
        if (!is_string($internalNote)) {
            throw new OrderConstraintException('Invalid internal note provided. Internal note must be a string.', OrderConstraintException::INVALID_INTERNAL_NOTE);
        }
    }
}
