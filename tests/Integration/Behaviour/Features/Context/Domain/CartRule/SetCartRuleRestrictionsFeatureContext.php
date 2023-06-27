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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\SetCartRuleRestrictionsCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRuleGroup;
use RuntimeException;

class SetCartRuleRestrictionsFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * @var array<string, SetCartRuleRestrictionsCommand>
     */
    private array $restrictionCommandsByReference = [];

    /**
     * @var array<string, RestrictionRuleGroup[]>
     */
    private array $productRestrictionGroupsByReference = [];

    /**
     * This is just a random number which in theory should never be reached as cart rule id in tests
     */
    private const NON_EXISTING_CART_RULE_ID = 54440051;

    /**
     * @When I restrict following cart rules for cart rule :cartRuleReference:
     *
     * @param string $cartRuleReference
     * @param TableNode $tableNode
     *
     * @return void
     */
    public function setRestrictedCartRules(string $cartRuleReference, TableNode $tableNode): void
    {
        $command = $this->getRestrictionsCommand($cartRuleReference);

        try {
            $command->setRestrictedCartRuleIds($this->referencesToIds($tableNode->getRowsHash()['restricted cart rules']));
            $this->restrictionCommandsByReference[$cartRuleReference] = $command;
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
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
        $rules = [];

        try {
            if ($table) {
                foreach ($table->getColumnsHash() as $row) {
                    $rules[] = new RestrictionRule($row['type'], $this->referencesToIds($row['references']));
                }
            }

            $restrictionGroups = [];
            if (isset($this->productRestrictionGroupsByReference[$cartRuleReference])) {
                $restrictionGroups = $this->productRestrictionGroupsByReference[$cartRuleReference];
            }

            $restrictionGroups[] = new RestrictionRuleGroup($quantity, $rules);
            $this->productRestrictionGroupsByReference[$cartRuleReference] = $restrictionGroups;
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
        $command = $this->getRestrictionsCommand($cartRuleReference);
        $command->setRestrictedCartRuleIds([self::NON_EXISTING_CART_RULE_ID]);

        try {
            $this->getCommandBus()->handle($command);
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
        $command = $this->getRestrictionsCommand($cartRuleReference);
        $command->setProductRestrictionRuleGroups([]);

        $this->restrictionCommandsByReference[$cartRuleReference] = $command;
        $this->productRestrictionGroupsByReference[$cartRuleReference] = [];
    }

    /**
     * @When I clear cart rule combination restrictions for cart rule :cartRuleReference
     *
     * @param string $cartRuleReference
     *
     * @return void
     */
    public function clearCartRuleCombinationRestrictions(string $cartRuleReference): void
    {
        $command = $this->getRestrictionsCommand($cartRuleReference);
        $command->setRestrictedCartRuleIds([]);

        $this->restrictionCommandsByReference[$cartRuleReference] = $command;
    }

    /**
     * @When I save all the restrictions for cart rule :cartRuleReference
     *
     * @param string $cartRuleReference
     *
     * @return void
     */
    public function saveRestrictionRules(string $cartRuleReference): void
    {
        $command = $this->getRestrictionsCommand($cartRuleReference);

        if (isset($this->productRestrictionGroupsByReference[$cartRuleReference])) {
            // set the restrictions that (if) were added step by step before
            $command->setProductRestrictionRuleGroups($this->productRestrictionGroupsByReference[$cartRuleReference]);
            unset($this->productRestrictionGroupsByReference[$cartRuleReference]);
        }

        if (null === $command->getRestrictedCartRuleIds() && null === $command->getProductRestrictionRuleGroups()) {
            throw new RuntimeException(
                sprintf(
                    '%s is empty for cart rule referenced as "%s". Did you forget to fill the restrictions in other steps?',
                    $command::class,
                    $cartRuleReference
                )
            );
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }

    private function getRestrictionsCommand(string $cartRuleReference): SetCartRuleRestrictionsCommand
    {
        if (isset($this->restrictionCommandsByReference[$cartRuleReference])) {
            $command = $this->restrictionCommandsByReference[$cartRuleReference];
            if (!($command instanceof SetCartRuleRestrictionsCommand)) {
                throw new RuntimeException(sprintf('Expected "%s" got "%s', SetCartRuleRestrictionsCommand::class, var_export($command, true)));
            }
        } else {
            $command = new SetCartRuleRestrictionsCommand($this->getSharedStorage()->get($cartRuleReference));
        }

        return $command;
    }
}
