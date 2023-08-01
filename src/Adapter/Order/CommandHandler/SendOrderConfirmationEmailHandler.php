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

use Order;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendOrderConfirmationEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\SendOrderConfirmationEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommandHandler]
class SendOrderConfirmationEmailHandler implements SendOrderConfirmationEmailHandlerInterface
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var int
     */
    private $contextEmployeeId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param TranslatorInterface $translator
     * @param ValidatorInterface $validator
     * @param int $contextShopId
     * @param int $contextLanguageId
     * @param int $contextEmployeeId
     */
    public function __construct(
        TranslatorInterface $translator,
        ValidatorInterface $validator,
        int $contextShopId,
        int $contextLanguageId,
        int $contextEmployeeId
    ) {
        $this->contextShopId = $contextShopId;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextEmployeeId = $contextEmployeeId;
        $this->translator = $translator;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CustomerMessageException
     * @throws OrderNotFoundException
     */
    public function handle(SendOrderConfirmationEmailCommand $command): void
    {
        $order = new Order($command->getOrderId()->getValue());
        dump($order);
        die;
    }
}
