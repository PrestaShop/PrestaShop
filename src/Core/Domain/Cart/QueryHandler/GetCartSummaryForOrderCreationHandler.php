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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\CreateEmptyCustomerCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartSummary;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartSummaryForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetLastEmptyCustomerCart;

final class GetCartSummaryForOrderCreationHandler implements GetCartSummaryForOrderCreationHandlerInterface
{
    /**
     * @var GetLastEmptyCustomerCartHandlerInterface
     */
    private $getLastEmptyCustomerCartHandler;

    /**
     * @var CreateEmptyCustomerCartHandlerInterface
     */
    private $createEmptyCartForCustomerHandler;

    /**
     * @var GetCartSummaryHandlerInterface
     */
    private $getCartSummaryHandler;

    public function __construct(
        GetLastEmptyCustomerCartHandlerInterface $getLastEmptyCustomerCartHandler,
        CreateEmptyCustomerCartHandlerInterface $createEmptyCartForCustomerHandler,
        GetCartSummaryHandlerInterface $getCartSummaryHandler
    ) {
        $this->getLastEmptyCustomerCartHandler = $getLastEmptyCustomerCartHandler;
        $this->createEmptyCartForCustomerHandler = $createEmptyCartForCustomerHandler;
        $this->getCartSummaryHandler = $getCartSummaryHandler;
    }

    public function handle(GetCartSummaryForOrderCreation $query)
    {
        $customerId = $query->getCustomerId();

        $cartId = $this->getLastEmptyCustomerCartHandler->handle(
            new GetLastEmptyCustomerCart($customerId->getValue())
        );

        if (null === $cartId) {
            $cartId = $this->createEmptyCartForCustomerHandler->handle(
                new CreateEmptyCustomerCartCommand($customerId->getValue())
            );
        }

        return $this->getCartSummaryHandler->handle(
            new GetCartSummary($cartId->getValue())
        );
    }
}
