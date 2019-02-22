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

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\CreateEmptyCustomerCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartSummaryForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetLastEmptyCustomerCart;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartSummaryForOrderCreationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetLastEmptyCustomerCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\ChooseCustomerForOrderCreationCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\ChooseCustomerForOrderCreationHandlerInterface;

final class ChooseCustomerForOrderCreationHandler implements ChooseCustomerForOrderCreationHandlerInterface
{
    /**
     * @var GetLastEmptyCustomerCartHandlerInterface
     */
    private $getLastEmptyCustomerCartHandler;

    /**
     * @var CreateEmptyCustomerCartHandlerInterface
     */
    private $createEmptyCustomerCartHandler;

    /**
     * @var GetCartSummaryForOrderCreationHandlerInterface
     */
    private $getCartSummaryForOrderCreationHandler;

    public function __construct(
        GetLastEmptyCustomerCartHandlerInterface $getLastEmptyCustomerCartHandler,
        CreateEmptyCustomerCartHandlerInterface $createEmptyCustomerCartHandler,
        GetCartSummaryForOrderCreationHandlerInterface $getCartSummaryForOrderCreationHandler
    ) {
        $this->getLastEmptyCustomerCartHandler = $getLastEmptyCustomerCartHandler;
        $this->createEmptyCustomerCartHandler = $createEmptyCustomerCartHandler;
        $this->getCartSummaryForOrderCreationHandler = $getCartSummaryForOrderCreationHandler;
    }

    public function handle(ChooseCustomerForOrderCreationCommand $command)
    {
        $customerId = $command->getCustomerId();

        $cartId = $this->getLastEmptyCustomerCartHandler->handle(new GetLastEmptyCustomerCart(
            $customerId->getValue()
        ));

        if (null === $cartId) {
            $cartId = $this->createEmptyCustomerCartHandler->handle(new CreateEmptyCustomerCartCommand(
                $customerId->getValue()
            ));
        }

        $summarizedCart = $this->getCartSummaryForOrderCreationHandler->handle(
            new GetCartSummaryForOrderCreation($cartId->getValue())
        );

        return [
            'summarized_cart' => $summarizedCart,
            'placed_orders' => [],
            'previous_carts' => [],
        ];
    }
}
