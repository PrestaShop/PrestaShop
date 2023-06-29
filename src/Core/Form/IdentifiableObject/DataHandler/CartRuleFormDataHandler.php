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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\CartRule\CartRuleActionBuilder;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class CartRuleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CartRuleActionBuilder
     */
    private $cartRuleActionBuilder;

    public function __construct(
        CommandBusInterface $commandBus,
        CartRuleActionBuilder $cartRuleActionBuilder
    ) {
        $this->commandBus = $commandBus;
        $this->cartRuleActionBuilder = $cartRuleActionBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        $informationData = $data['information'];
        $conditionsData = $data['conditions'];
        $dateRange = $conditionsData['valid_date_range'];

        $command = new AddCartRuleCommand(
            $informationData['name'],
            $this->cartRuleActionBuilder->build($data['actions']),
            $data['conditions']['shop_association']
        );

        $command
            ->setCode($informationData['code'])
            ->setHighlightInCart(isset($informationData['highlight']) && (bool) $informationData['highlight'])
            ->setAllowPartialUse((bool) $informationData['partial_use'])
            ->setPriority((int) $informationData['priority'])
            ->setActive((bool) $informationData['active'])
            ->setValidityDateRange(
                DateTimeImmutable::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $dateRange['from']),
                DateTimeImmutable::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $dateRange['to'])
            )
            ->setTotalQuantity((int) $conditionsData['total_available'])
            ->setQuantityPerUser((int) $conditionsData['available_per_user'])
        ;

        if (!empty($conditionsData['minimum_amount']['amount'])) {
            $amountData = $conditionsData['minimum_amount'];
            $command->setMinimumAmount(
                (string) $amountData['amount'],
                (int) $amountData['currency'],
                (bool) $amountData['tax_included'],
                (bool) $amountData['shipping_included']
            );
        }

        if (!empty($conditionsData['customer'][0]['id_customer'])) {
            $command->setCustomerId((int) $conditionsData['customer'][0]['id_customer']);
        }

        $this->commandBus->handle($command);
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }
}
