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

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageConstraintException;

/**
 * Add new order message
 */
class AddOrderMessageCommand
{
    /**
     * @var string[]
     */
    private $localizedName;

    /**
     * @var string[]
     */
    private $localizedMessage;

    /**
     * @param string[] $localizedName
     * @param string[] $localizedMessage
     */
    public function __construct(array $localizedName, array $localizedMessage)
    {
        if (empty($localizedName)) {
            throw new OrderMessageConstraintException('OrderMessage name must not be empty');
        }

        if (empty($localizedMessage)) {
            throw new OrderMessageConstraintException('OrderMessage message must not be empty');
        }

        $this->localizedName = $localizedName;
        $this->localizedMessage = $localizedMessage;
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
