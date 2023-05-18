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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;

class AddCartRuleFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * @When I create cart rule :cartRuleReference with following properties:
     * @When there is a cart rule :cartRuleReference with following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $node
     */
    public function createCartRuleIfNotExists(string $cartRuleReference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);

        if ($this->getSharedStorage()->exists($cartRuleReference)) {
            try {
                // if cart rule already exists we assert all its expected properties
                $this->assertCartRuleProperties(
                    $this->getCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)),
                    $data
                );

                return;
            } catch (CartRuleNotFoundException $e) {
                // if cart rule was not found we proceed to create it under this reference.
            }
        }

        try {
            $this->createCartRuleWithReference($cartRuleReference, $data);
        } catch (CartRuleConstraintException $e) {
            $this->setLastException($e);
        }
    }
}
