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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Adapter\Cart\Repository\CartRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\DeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\DeleteCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteOrderedCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Handles deletion of cart using legacy object model
 */
final class DeleteCartHandler extends AbstractCartHandler implements DeleteCartHandlerInterface
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param CartRepository $cartRepository
     */
    public function __construct(CartRepository $cartRepository, OrderRepository $orderRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteCartException
     * @throws CannotDeleteOrderedCartException
     * @throws CartException
     * @throws CoreException
     */
    public function handle(DeleteCartCommand $command): void
    {
        $order = $this->orderRepository->findByCartId($command->getCartId());
        if ($order) {
            throw new CannotDeleteOrderedCartException(sprintf('Cart "%s" with order cannot be deleted.', $command->getCartId()->getValue()));
        }
        $this->cartRepository->delete($command->getCartId());
    }
}
