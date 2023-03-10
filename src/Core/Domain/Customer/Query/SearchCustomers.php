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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Searchers for customers by phrases matching customer's first name, last name, email, company name and id
 */
class SearchCustomers
{
    /**
     * @var string[]
     */
    private $phrases;

    /**
     * @var ShopConstraint|null
     */
    private $shopConstraint;

    /**
     * @param string[] $phrases
     */
    public function __construct(
        array $phrases,
        ?ShopConstraint $shopConstraint = null
    ) {
        $this->assertPhrasesAreNotEmpty($phrases);
        $this->assertShopConstraintIsSupported($shopConstraint);
        $this->phrases = $phrases;
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return string[]
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

    /**
     * @return ShopConstraint|null
     */
    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param string[] $phrases
     */
    private function assertPhrasesAreNotEmpty(array $phrases)
    {
        if (empty($phrases)) {
            throw new CustomerException('Phrases cannot be empty when searching customers.');
        }
    }

    private function assertShopConstraintIsSupported(?ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint && $shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop group constraint is not supported');
        }
    }
}
