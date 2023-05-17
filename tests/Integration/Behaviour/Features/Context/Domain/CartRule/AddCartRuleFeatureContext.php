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

namespace Tests\Integration\Behaviour\Features\Context\Domain\CartRule;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class AddCartRuleFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * @When I create cart rule :cartRuleReference with following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $node
     */
    public function createCartRuleWithReference(string $cartRuleReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        try {
            $command = new AddCartRuleCommand(
                $data['name'],
                $this->getCartRuleActionBuilder()->build($this->formatDataForActionBuilder($data))
            );

            if (isset($data['highlight'])) {
                $command->setHighlightInCart(PrimitiveUtils::castStringBooleanIntoBoolean($data['highlight']));
            }
            if (isset($data['allow_partial_use'])) {
                $command->setAllowPartialUse(PrimitiveUtils::castStringBooleanIntoBoolean($data['allow_partial_use']));
            }
            if (isset($data['priority'])) {
                $command->setPriority((int) $data['priority']);
            }
            if (isset($data['active'])) {
                $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
            }
            if (isset($data['valid_from'])) {
                if (empty($data['valid_to'])) {
                    throw new RuntimeException('When setting cart rule range "valid_from" and "valid_to" must be provided');
                }
                $command->setValidDateRange(
                    new DateTime($data['valid_from']),
                    new DateTime($data['valid_to']),
                );
            }
            if (isset($data['total_quantity'])) {
                $command->setTotalQuantity((int) $data['total_quantity']);
            }
            if (isset($data['quantity_per_user'])) {
                $command->setQuantityPerUser((int) $data['quantity_per_user']);
            }
            if (!empty($data['minimum_amount'])) {
                $currencyId = $this->getSharedStorage()->get($data['minimum_amount_currency']);
                $command->setMinimumAmount(
                    $data['minimum_amount'],
                    $currencyId,
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_tax_included']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['minimum_amount_shipping_included'])
                );
            }

            $command->setDescription($data['description'] ?? '');
            if (!empty($data['code'])) {
                $command->setCode($data['code']);
            }

            /** @var CartRuleId $cartRuleId */
            $cartRuleId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($cartRuleReference, $cartRuleId->getValue());

            if (!empty($data['code'])) {
                // set cart rule id by code when it is not empty
                $this->getSharedStorage()->set($data['code'], $cartRuleId->getValue());
            }
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }
}
