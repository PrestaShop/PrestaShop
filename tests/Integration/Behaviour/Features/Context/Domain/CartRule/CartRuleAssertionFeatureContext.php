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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\CartRuleForEditing;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;

class CartRuleAssertionFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * @Then cart rule with reference :cartRuleReference is enabled
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     * @throws RuntimeException
     */
    public function assertCartRuleEnabled(string $cartRuleReference): void
    {
        /** @var CartRuleForEditing $cartRule */
        $cartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)));

        Assert::assertTrue(
            $cartRule->getInformation()->isEnabled(),
            sprintf('Cart rule %s is not enabled', $cartRuleReference)
        );
    }

    /**
     * @Then cart rule with reference :cartRuleReference is disabled
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     */
    public function assertCartRuleDisabled(string $cartRuleReference): void
    {
        /** @var CartRuleForEditing $cartRule */
        $cartRule = $this->getQueryBus()->handle(new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)));

        Assert::assertFalse(
            $cartRule->getInformation()->isEnabled(),
            sprintf('Cart rule %s is not disabled', $cartRuleReference)
        );
    }

    /**
     * @Then Cart rule with reference :cartRuleReference does not exist
     *
     * @param string $cartRuleReference
     *
     * @throws CartRuleConstraintException
     * @throws NoExceptionAlthoughExpectedException
     */
    public function assertCartRuleDeleted(string $cartRuleReference): void
    {
        try {
            $this->getQueryBus()->handle(new GetCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)));
            throw new NoExceptionAlthoughExpectedException(sprintf('Cart rule "%s" was found, but it was expected to be deleted', $cartRuleReference));
        } catch (CartRuleNotFoundException $e) {
            $this->getSharedStorage()->clear($cartRuleReference);
        }
    }

    /**
     * @Then I should get cart rule error about :error
     *
     * @param string $error
     *
     * @return void
     */
    public function assertCartRuleError(string $error): void
    {
        $errorMap = [
            'required specific product' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::MISSING_DISCOUNT_APPLICATION_PRODUCT,
            ],
            'non unique cart rule code' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::NON_UNIQUE_CODE,
            ],
            'missing action' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::MISSING_ACTION,
            ],
            'invalid cart rule restriction' => [
                'class' => CartRuleConstraintException::class,
                'code' => CartRuleConstraintException::INVALID_CART_RULE_RESTRICTION,
            ],
            'non-existing cart rule' => [
                'class' => CartRuleNotFoundException::class,
                'code' => 0,
            ],
        ];

        if (!isset($errorMap[$error])) {
            throw new RuntimeException(sprintf('$error "%s" not set in error map for assertion', $error));
        }

        $this->assertLastErrorIs(
            $errorMap[$error]['class'],
            $errorMap[$error]['code']
        );
    }

    /**
     * @Then cart rule :cartRuleReference should have the following properties:
     *
     * @param string $cartRuleReference
     * @param TableNode $tableNode
     */
    public function assertCartRule(string $cartRuleReference, TableNode $tableNode): void
    {
        $this->assertCartRuleProperties(
            $this->getCartRuleForEditing($this->getSharedStorage()->get($cartRuleReference)),
            $this->localizeByColumns($tableNode)
        );
    }
}
