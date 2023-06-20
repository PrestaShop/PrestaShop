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

class RestrictionRule
{
    public const TYPE_PRODUCT = 'products';
    public const TYPE_CATEGORY = 'categories';
    public const TYPE_ATTRIBUTE = 'attributes';
    public const TYPE_MANUFACTURERS = 'manufacturers';
    public const TYPE_SUPPLIER = 'suppliers';
    public const VALID_TYPES = [
        self::TYPE_PRODUCT => self::TYPE_PRODUCT,
        self::TYPE_CATEGORY => self::TYPE_CATEGORY,
        self::TYPE_ATTRIBUTE => self::TYPE_ATTRIBUTE,
        self::TYPE_MANUFACTURERS => self::TYPE_MANUFACTURERS,
        self::TYPE_SUPPLIER => self::TYPE_SUPPLIER,
    ];

    /**
     * @param string $type
     * @param int[] $ids Ids of product, attributes, categories, manufacturers or suppliers, depending on restriction rule type
     */
    public function __construct(
        public readonly string $type,
        public readonly array $ids
    ) {
        $this->assertType($type);
        $this->assertIds($ids);
    }

    /**
     * @param int[] $ids
     *
     * @return void
     *
     * @throws CartRuleConstraintException
     */
    private function assertIds(array $ids): void
    {
        if (empty($ids)) {
            throw new CartRuleConstraintException(
                sprintf('%s item ids cannot be empty', self::class),
                CartRuleConstraintException::EMPTY_RESTRICTION_RULE_IDS
            );
        }
        foreach ($ids as $id) {
            if (!is_int($id) || $id <= 0) {
                throw new CartRuleConstraintException(
                    sprintf('%s item id must be positive integer', self::class),
                    CartRuleConstraintException::INVALID_RESTRICTION_RULE_ID
                );
            }
        }
    }

    private function assertType(string $type): void
    {
        if (in_array($type, self::VALID_TYPES, true)) {
            return;
        }

        throw new CartRuleConstraintException(
          sprintf('Invalid type provided to %s', self::class),
            CartRuleConstraintException::INVALID_RESTRICTION_RULE_TYPE
        );
    }
}
