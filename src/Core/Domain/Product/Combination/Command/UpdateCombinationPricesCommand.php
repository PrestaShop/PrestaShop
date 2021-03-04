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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

class UpdateCombinationPricesCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var DecimalNumber|null
     */
    private $impactOnPrice;

    /**
     * @var DecimalNumber|null
     */
    private $ecoTax;

    /**
     * @var DecimalNumber|null
     */
    private $impactOnUnitPrice;

    /**
     * @var DecimalNumber|null
     */
    private $wholesalePrice;

    /**
     * @param int $combinationId
     */
    public function __construct(
        int $combinationId
    ) {
        $this->combinationId = new CombinationId($combinationId);
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getImpactOnPrice(): ?DecimalNumber
    {
        return $this->impactOnPrice;
    }

    /**
     * @param string $impactOnPrice
     *
     * @return UpdateCombinationPricesCommand
     */
    public function setImpactOnPrice(string $impactOnPrice): UpdateCombinationPricesCommand
    {
        $this->impactOnPrice = new DecimalNumber($impactOnPrice);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getEcoTax(): ?DecimalNumber
    {
        return $this->ecoTax;
    }

    /**
     * @param string $ecoTax
     *
     * @return UpdateCombinationPricesCommand
     */
    public function setEcoTax(string $ecoTax): UpdateCombinationPricesCommand
    {
        $this->ecoTax = new DecimalNumber($ecoTax);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getImpactOnUnitPrice(): ?DecimalNumber
    {
        return $this->impactOnUnitPrice;
    }

    /**
     * @param string $impactOnUnitPrice
     *
     * @return UpdateCombinationPricesCommand
     */
    public function setImpactOnUnitPrice(string $impactOnUnitPrice): UpdateCombinationPricesCommand
    {
        $this->impactOnUnitPrice = new DecimalNumber($impactOnUnitPrice);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getWholesalePrice(): ?DecimalNumber
    {
        return $this->wholesalePrice;
    }

    /**
     * @param string $wholesalePrice
     *
     * @return UpdateCombinationPricesCommand
     */
    public function setWholesalePrice(string $wholesalePrice): UpdateCombinationPricesCommand
    {
        $this->wholesalePrice = new DecimalNumber($wholesalePrice);

        return $this;
    }
}
