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

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Edit given order message
 */
class EditOrderMessageCommand
{
    /**
     * @var OrderMessageId
     */
    private $orderMessageId;

    /**
     * @var string[]|null
     */
    private $localizedName;

    /**
     * @var string[]|null
     */
    private $localizedMessage;

    /**
     * @param int $orderMessageId
     * @param string[]|null $localizedName Array of localized name or null if name should not be edited
     * @param string[]|null $localizedMessage Array of localized message or null if message should not be edited
     */
    public function __construct(int $orderMessageId, array $localizedName = null, array $localizedMessage = null)
    {
        $this->orderMessageId = new OrderMessageId($orderMessageId);
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
     * @return string[]|null
     */
    public function getLocalizedName(): ?array
    {
        return $this->localizedName;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMessage(): ?array
    {
        return $this->localizedMessage;
    }
}
