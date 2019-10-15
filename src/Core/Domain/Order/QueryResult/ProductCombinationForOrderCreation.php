<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class ProductCombinationForOrderCreation
{
    /**
     * @var int
     */
    private $attributeCombinationId;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var string
     */
    private $formattedPrice;

    /**
     * @param int $attributeCombinationId
     * @param string $attribute
     * @param int $stock
     * @param string $formattedPrice
     */
    public function __construct(int $attributeCombinationId, string $attribute, int $stock, string $formattedPrice)
    {
        $this->attributeCombinationId = $attributeCombinationId;
        $this->attribute = $attribute;
        $this->stock = $stock;
        $this->formattedPrice = $formattedPrice;
    }

    /**
     * @return int
     */
    public function getAttributeCombinationId(): int
    {
        return $this->attributeCombinationId;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return $this->formattedPrice;
    }

    /**
     * @param string $name
     */
    public function appendAttributeName(string $name)
    {
        $this->attribute .= ' - ' . $name;
    }
}
