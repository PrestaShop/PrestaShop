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

namespace Tests\Integration\Behaviour\Features\Context\Domain\CartRule;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\BulkToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\SetCartRuleRestrictionsCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\SetCartRuleProductRestrictionsCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRuleGroup;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class EditCartRuleFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * This is just a random number which in theory should never be reached as cart rule id in tests
     */
    private const NON_EXISTING_CART_RULE_ID = 54440051;

    /**
     * Provides string to set and retrieve certain cart rule product restrictions state from shared storage
     *
     * @param string $cartRuleReference
     *
     * @return string
     */
    public static function buildProductRestrictionStorageKey(string $cartRuleReference): string
    {
        return 'cart_rule_product_restriction_groups_' . $cartRuleReference;
    }

    /**
     * @When /^I (enable|disable) cart rule with reference "(.+)"$/
     *
     * @param bool $enable
     * @param string $cartRuleReference
     *
     * @see StringToBoolTransformContext::transformTruthyStringToBoolean for $enable string to bool transformation
     */
    public function toggleCartRuleStatus(bool $enable, string $cartRuleReference): void
    {
        $this->getCommandBus()->handle(
            new ToggleCartRuleStatusCommand($this->getSharedStorage()->get($cartRuleReference), $enable)
        );
    }

    /**
     * @When /^I bulk (enable|disable) cart rules "(.+)"$/
     *
     * @param string $cartRuleReferences
     */
    public function bulkEnableCartRules(bool $enable, string $cartRuleReferences): void
    {
        $this->getCommandBus()->handle(
            new BulkToggleCartRuleStatusCommand($this->referencesToIds($cartRuleReferences), $enable)
        );
    }

    /**
     * @When I edit cart rule :cartRuleReference with following properties:
     *
     * @param TableNode $tableNode
     */
    public function editCartRule(string $cartRuleReference, TableNode $tableNode): void
    {
        try {
            $cartRuleId = $this->getSharedStorage()->get($cartRuleReference);
            $command = new EditCartRuleCommand($cartRuleId);
            $data = $this->localizeByRows($tableNode);
            $this->fillCommand($command, $data);
            $this->getCommandBus()->handle($command);

            if (!empty($data['code'])) {
                // resets cart rule id by the code in storage if it was edited
                $this->getSharedStorage()->set($data['code'], $cartRuleId);
            }
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I restrict following cart rules for cart rule :cartRuleReference:
     *
     * @param string $cartRuleReference
     * @param TableNode $tableNode
     *
     * @return void
     */
    public function restrictCartRules(string $cartRuleReference, TableNode $tableNode): void
    {
        $restrictedCartRuleIds = [];
        foreach ($tableNode->getColumn(0) as $restrictedCartRuleReference) {
            $restrictedCartRuleIds[] = $this->getSharedStorage()->get($restrictedCartRuleReference);
        }

        try {
            $this->performCartRulesRestriction($this->getSharedStorage()->get($cartRuleReference), $restrictedCartRuleIds);
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     *
     * @When I add a restriction for cart rule :cartRuleReference, which requires at least :quantity product(s) in cart matching one of these rules:
     * @When I add a restriction for cart rule :cartRuleReference, which requires any quantity of product(s) in cart matching one of these rules:
     * @When I add a restriction for cart rule :cartRuleReference, which requires at least :quantity product(s), but I provide empty list of rules
     *
     * @param string $cartRuleReference
     * @param int $quantity
     * @param TableNode|null $table
     *
     * @return void
     */
    public function addRestrictionRule(string $cartRuleReference, int $quantity = 0, ?TableNode $table = null): void
    {
        $restrictionsKey = $this::buildProductRestrictionStorageKey($cartRuleReference);
        $rules = [];

        try {
            if ($table) {
                foreach ($table->getColumnsHash() as $row) {
                    $rules[] = new RestrictionRule($row['type'], $this->referencesToIds($row['references']));
                }
            }

            $restrictionGroups = [];
            if ($this->getSharedStorage()->exists($restrictionsKey)) {
                $restrictionGroups = $this->getSharedStorage()->get($restrictionsKey);
            }

            $restrictionGroups[] = new RestrictionRuleGroup($quantity, $rules);
            $this->getSharedStorage()->set($restrictionsKey, $restrictionGroups);
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I restrict cart rules for :cartRuleReference providing non-existing cart rules
     *
     * @param string $cartRuleReference
     *
     * @return void
     */
    public function restrictCartRulesProvidingNonExistingIds(string $cartRuleReference): void
    {
        try {
            $this->performCartRulesRestriction(
                $this->getSharedStorage()->get($cartRuleReference),
                [self::NON_EXISTING_CART_RULE_ID]
            );
        } catch (CartRuleNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I clear all product restrictions for cart rule :cartRuleReference
     *
     * @param string $cartRuleReference
     *
     * @return void
     */
    public function clearProductRestrictionRules(string $cartRuleReference): void
    {
        $restrictionsKey = $this::buildProductRestrictionStorageKey($cartRuleReference);

        $this->getCommandBus()->handle(new SetCartRuleProductRestrictionsCommand(
            $this->getSharedStorage()->get($cartRuleReference),
            []
        ));

        $this->getSharedStorage()->clear($restrictionsKey);
    }

    /**
     * @When I save product restrictions for cart rule :cartRuleReference
     *
     * @param string $cartRuleReference
     *
     * @return void
     */
    public function saveProductRestrictionRules(string $cartRuleReference): void
    {
        $restrictionsKey = $this::buildProductRestrictionStorageKey($cartRuleReference);

        $this->getCommandBus()->handle(new SetCartRuleProductRestrictionsCommand(
            $this->getSharedStorage()->get($cartRuleReference),
            $this->getSharedStorage()->get($restrictionsKey)
        ));

        $this->getSharedStorage()->clear($restrictionsKey);
    }

    private function performCartRulesRestriction(int $cartRuleId, array $restrictedCartRuleIds): void
    {
        $this->getCommandBus()->handle(
            new SetCartRuleRestrictionsCommand(
                $cartRuleId,
                $restrictedCartRuleIds
            )
        );
    }

    /**
     * @param EditCartRuleCommand $command
     * @param array<string, mixed> $data
     */
    private function fillCommand(EditCartRuleCommand $command, array $data): void
    {
        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (isset($data['highlight'])) {
            $command->setHighlightInCart(PrimitiveUtils::castStringBooleanIntoBoolean($data['highlight']));
        }
        if (isset($data['allow_partial_use'])) {
            $command->setAllowPartialUse(PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']));
        }
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['code'])) {
            $command->setCode($data['code']);
        }
        if (isset($data['customer'])) {
            $command->setCustomerId(!empty($data['customer']) ? $this->getSharedStorage()->get($data['customer']) : 0);
        }
        if (isset($data['priority'])) {
            $command->setPriority((int) $data['priority']);
        }
        if (isset($data['valid_from'])) {
            $command->setValidityDateRange(
                new DateTimeImmutable($data['valid_from']),
                new DateTimeImmutable($data['valid_to'])
            );
        }
        if (isset($data['total_quantity'])) {
            $command->setTotalQuantity((int) $data['total_quantity']);
        }
        if (isset($data['quantity_per_user'])) {
            $command->setQuantityPerUser((int) $data['quantity_per_user']);
        }
        if (isset($data['minimum_amount'])) {
            $command->setMinimumAmount(
                $data['minimum_amount'],
                $this->getSharedStorage()->get($data['minimum_amount_currency']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included'])
            );
        }

        $cartRuleActionBuilder = $this->getCartRuleActionBuilder();
        $formattedActionData = $this->formatDataForActionBuilder($data);

        if ($cartRuleActionBuilder->supports($formattedActionData)) {
            $command->setCartRuleAction($cartRuleActionBuilder->build($formattedActionData));
        }
    }
}
