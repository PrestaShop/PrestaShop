<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\MerchandiseReturn;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShopException;

/**
 * Provides reusable methods for merchandise return command/query handlers
 */
abstract class AbstractMerchandiseReturnHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy OrderReturn
     *
     * @param MerchandiseReturnId $merchandiseReturnId
     *
     * @return OrderReturn
     *
     * @throws MerchandiseReturnException
     */
    protected function getOrderReturn(MerchandiseReturnId $merchandiseReturnId)
    {
        try {
            $orderReturn = new OrderReturn($merchandiseReturnId->getValue());
        } catch (PrestaShopException $e) {
            throw new MerchandiseReturnException('Failed to create new order return', 0, $e);
        }

        if ($orderReturn->id !== $merchandiseReturnId->getValue()) {
            throw new MerchandiseReturnNotFoundException(sprintf('Merchandise return with id "%s" was not found.', $orderReturn->getValue()));
        }

        return $orderReturn;
    }
}
