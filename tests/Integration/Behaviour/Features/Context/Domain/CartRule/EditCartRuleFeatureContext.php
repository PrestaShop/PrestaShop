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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\ToggleCartRuleStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class EditCartRuleFeatureContext extends AbstractCartRuleFeatureContext
{
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
            $this->fillEditCommand($command, $data);
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
     * @param EditCartRuleCommand $command
     * @param array<string, mixed> $data
     */
    private function fillEditCommand(EditCartRuleCommand $command, array $data): void
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
