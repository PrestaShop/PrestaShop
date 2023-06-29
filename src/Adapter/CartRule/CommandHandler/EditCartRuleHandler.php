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

namespace PrestaShop\PrestaShop\Adapter\CartRule\CommandHandler;

use CartRule;
use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleActionFiller;
use PrestaShop\PrestaShop\Adapter\CartRule\Repository\CartRuleRepository;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\EditCartRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Shop;

class EditCartRuleHandler extends AbstractObjectModelHandler implements EditCartRuleHandlerInterface
{
    /**
     * @var CartRuleRepository
     */
    private $cartRuleRepository;

    /**
     * @var CartRuleActionFiller
     */
    private $cartRuleActionFiller;

    public function __construct(
        CartRuleRepository $cartRuleRepository,
        CartRuleActionFiller $cartRuleActionFiller
    ) {
        $this->cartRuleRepository = $cartRuleRepository;
        $this->cartRuleActionFiller = $cartRuleActionFiller;
    }

    public function handle(EditCartRuleCommand $command): void
    {
        $cartRule = $this->cartRuleRepository->get($command->getCartRuleId());
        $updatableProperties = $this->fillUpdatableProperties($cartRule, $command);

        if (empty($updatableProperties)) {
            return;
        }

        $this->cartRuleRepository->partialUpdate($cartRule, $updatableProperties);

        if (null !== $command->getAssociatedShopIds()) {
            // by default table shop association doesn't exist because legacy used to handle it differently,
            // but it seems to work just fine handling it as all other entities shop associations
            Shop::addTableAssociation('cart_rule', ['type' => 'shop']);

            $this->associateWithShops($cartRule, array_map(static function (ShopId $shopId) {
                return $shopId->getValue();
            }, $command->getAssociatedShopIds()));
        }
    }

    /**
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     *
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillUpdatableProperties(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $propertiesToUpdate = [];
        if (null !== $command->getLocalizedNames()) {
            $cartRule->name = $command->getLocalizedNames();
            $propertiesToUpdate['name'] = array_keys($command->getLocalizedNames());
        }
        if (null !== $command->getDescription()) {
            $cartRule->description = $command->getDescription();
            $propertiesToUpdate[] = 'description';
        }
        if (null !== $command->getCode()) {
            $cartRule->code = $command->getCode();
            $propertiesToUpdate[] = 'code';
        }
        if (null !== $command->highlightInCart()) {
            $cartRule->highlight = $command->highlightInCart();
            $propertiesToUpdate[] = 'highlight';
        }
        if (null !== $command->allowPartialUse()) {
            $cartRule->partial_use = $command->allowPartialUse();
            $propertiesToUpdate[] = 'partial_use';
        }
        if (null !== $command->getPriority()) {
            $cartRule->priority = $command->getPriority();
            $propertiesToUpdate[] = 'priority';
        }
        if (null !== $command->isActive()) {
            $cartRule->active = $command->isActive();
            $propertiesToUpdate[] = 'active';
        }
        if (null !== $command->getAssociatedShopIds()) {
            $cartRule->shop_restriction = true;
            $propertiesToUpdate[] = 'shop_restriction';
        }

        $conditionsToUpdate = $this->fillConditions($cartRule, $command);
        $actionsToUpdate = $this->fillActions($cartRule, $command);

        return array_merge($propertiesToUpdate, $conditionsToUpdate, $actionsToUpdate);
    }

    /**
     * Fills cart rule with conditions data from command.
     *
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     *
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillConditions(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getCustomerId()) {
            $cartRule->id_customer = $command->getCustomerId()->getValue();
            $updatableProperties[] = 'id_customer';
        }
        if (null !== $command->getValidFrom()) {
            $cartRule->date_from = $command->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_from';
        }
        if (null !== $command->getValidTo()) {
            $cartRule->date_to = $command->getValidTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_to';
        }

        $minimumAmount = $command->getMinimumAmount();
        if (null !== $minimumAmount) {
            $cartRule->minimum_amount = (float) (string) $minimumAmount->getAmount();
            $cartRule->minimum_amount_currency = $minimumAmount->getCurrencyId()->getValue();
            $cartRule->minimum_amount_tax = $minimumAmount->isTaxIncluded();
            $cartRule->minimum_amount_shipping = $command->isMinimumAmountShippingIncluded();
            $updatableProperties = array_merge($updatableProperties, [
                'minimum_amount',
                'minimum_amount_currency',
                'minimum_amount_tax',
                'minimum_amount_shipping',
            ]);
        }
        if (null !== $command->getTotalQuantity()) {
            $cartRule->quantity = $command->getTotalQuantity();
            $updatableProperties[] = 'quantity';
        }
        if (null !== $command->getQuantityPerUser()) {
            $cartRule->quantity_per_user = $command->getQuantityPerUser();
            $updatableProperties[] = 'quantity_per_user';
        }

        return $updatableProperties;
    }

    /**
     * Fills cart rule with actions data from command.
     *
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     *
     * @return string[] updatable properties
     */
    private function fillActions(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $cartRuleAction = $command->getCartRuleAction();

        if (null === $cartRuleAction) {
            return [];
        }

        return $this->cartRuleActionFiller->fillUpdatableProperties($cartRule, $cartRuleAction);
    }
}
