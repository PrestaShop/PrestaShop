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

use DateTime;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionBuilderInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class CartRuleFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CartRuleActionBuilderInterface
     */
    private $cartRuleActionBuilder;

    public function __construct(
        CommandBusInterface $commandBus,
        CartRuleActionBuilderInterface $cartRuleActionBuilder
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
            isset($informationData['highlight']) && (bool) $informationData['highlight'],
            (bool) $informationData['partial_use'],
            (int) $informationData['priority'],
            (bool) $informationData['active'],
            DateTime::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $dateRange['from']),
            DateTime::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, $dateRange['to']),
            (int) $conditionsData['total_available'],
            (int) $conditionsData['available_per_user'],
            $this->buildCartRuleActionForCreate($data['actions'])
        );

        if (!empty($data['actions']['discount']['reduction']['value'])) {
            $command->setDiscountApplicationType($data['actions']['discount']['discount_application']);
            if (
                $data['actions']['discount']['discount_application'] === DiscountApplicationType::SPECIFIC_PRODUCT &&
                !empty($data['actions']['discount']['specific_product'][0]['id'])
            ) {
                $command->setDiscountProductId((int) $data['actions']['discount']['specific_product'][0]['id']);
            }
        }

        if (!empty($conditionsData['minimum_amount']['amount'])) {
            $amountData = $conditionsData['minimum_amount'];
            $command->setMinimumAmount(
                (string) $amountData['amount'],
                (int) $amountData['currency'],
                (bool) $amountData['tax_included'],
                (bool) $amountData['shipping_included']
            );
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

    private function buildCartRuleActionForCreate(array $actionsData): CartRuleActionInterface
    {
        $reductionType = null;
        $reductionValue = null;
        if (!empty($actionsData['discount']['reduction']['value'])) {
            $reductionValue = $actionsData['discount']['reduction']['value'];
            $reductionType = $actionsData['discount']['reduction']['type'];
        }

        $giftProductId = null;
        $giftCombinationId = null;
        if (!empty($actionsData['gift_product'][0])) {
            $giftProductId = (int) $actionsData['gift_product'][0]['product_id'];
            $giftCombinationId = !empty($actionsData['gift_product'][0]['combination_id']) ? (int) $actionsData['gift_product'][0]['combination_id'] : null;
        }

        return $this->cartRuleActionBuilder->build(
            (bool) $actionsData['free_shipping'],
            $reductionType,
            $reductionValue,
            !empty($actionsData['discount']['reduction']['currency']) ? $actionsData['discount']['reduction']['currency'] : null,
            (bool) $actionsData['discount']['reduction']['include_tax'],
            $giftProductId,
            $giftCombinationId,
            (bool) $actionsData['discount']['exclude_discounted_products']
        );
    }
}
