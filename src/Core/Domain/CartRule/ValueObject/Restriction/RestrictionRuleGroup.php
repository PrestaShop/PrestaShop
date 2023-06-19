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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class RestrictionRuleGroup
{
    /**
     * @var int
     */
    private $requiredQuantityInCart;

    /**
     * @var RestrictionRule[]
     */
    private $restrictionRules;

    public function __construct(
        int $requiredQuantityInCart,
        array $restrictionRules
    ) {
        $this->assertRestrictionRules($restrictionRules);
        $this->requiredQuantityInCart = $requiredQuantityInCart;
        $this->restrictionRules = $restrictionRules;
    }

    /**
     * @return int
     */
    public function getRequiredQuantityInCart(): int
    {
        return $this->requiredQuantityInCart;
    }

    /**
     * @return RestrictionRule[]
     */
    public function getRestrictionRules(): array
    {
        return $this->restrictionRules;
    }

    /**
     * @param RestrictionRule[] $rules
     *
     * @return void
     *
     * @throws CartRuleConstraintException
     * @throws InvalidArgumentException
     */
    private function assertRestrictionRules(array $rules): void
    {
        if (empty($rules)) {
            throw new CartRuleConstraintException(
                'Restriction rules list cannot be empty',
                CartRuleConstraintException::EMPTY_RESTRICTION_RULES
            );
        }

        foreach ($rules as $rule) {
            if ($rule instanceof RestrictionRule) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Invalid array element. Expected array of %s, but got %s',
                    RestrictionRule::class,
                    var_export($rule, true)
                )
            );
        }
    }
}
