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

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Transfers current order message data that can be edited
 */
class EditableOrderMessage
{
    /**
     * @var OrderMessageId
     */
    private $orderMessageId;

    /**
     * @var string[]
     */
    private $localizedName;

    /**
     * @var string[]
     */
    private $localizedMessage;

    /**
     * @param OrderMessageId $orderMessageId
     * @param string[] $localizedName
     * @param string[] $localizedMessage
     */
    public function __construct(OrderMessageId $orderMessageId, array $localizedName, array $localizedMessage)
    {
        $this->orderMessageId = $orderMessageId;
        $this->localizedName = $localizedName;
        $this->localizedMessage = $localizedMessage;
    }

    /**
     * @return OrderMessageId
     */
    public function getOrderMessageId(): OrderMessageId
    {
        return $this->orderMessageId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedName(): array
    {
        return $this->localizedName;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMessage(): array
    {
        return $this->localizedMessage;
    }
}
