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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds cart rule (a.k.a voucher) data for cart information
 */
class CartRule
{
    /**
     * @var int
     */
    private $cartRuleId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $value;

    /**
     * @param int $cartRuleId
     * @param string $name
     * @param string $description
     * @param string $value
     */
    public function __construct(
        int $cartRuleId,
        string $name,
        string $description,
        string $value
    ) {
        $this->cartRuleId = $cartRuleId;
        $this->name = $name;
        $this->description = $description;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getCartRuleId(): int
    {
        return $this->cartRuleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
