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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;

/**
 * Transfers combination data for listing
 */
class EditableCombinationForListing
{
    /**
     * @var int
     */
    private $combinationId;

    /**
     * @var CombinationAttributeInformation[]
     */
    private $attributesInformation;

    /**
     * @var string
     */
    private $combinationName;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var bool
     */
    private $default;

    /**
     * @var DecimalNumber
     */
    private $impactOnPrice;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var DecimalNumber
     */
    private $ecoTax;

    /**
     * @param int $combinationId
     * @param string $combinationName
     * @param string $reference
     * @param CombinationAttributeInformation[] $attributesInformation
     * @param bool $default
     * @param DecimalNumber $impactOnPrice
     * @param int $quantity
     * @param string $imageUrl
     * @param DecimalNumber $ecoTax
     */
    public function __construct(
        int $combinationId,
        string $combinationName,
        string $reference,
        array $attributesInformation,
        bool $default,
        DecimalNumber $impactOnPrice,
        int $quantity,
        string $imageUrl,
        DecimalNumber $ecoTax
    ) {
        $this->combinationId = $combinationId;
        $this->attributesInformation = $attributesInformation;
        $this->combinationName = $combinationName;
        $this->reference = $reference;
        $this->default = $default;
        $this->impactOnPrice = $impactOnPrice;
        $this->quantity = $quantity;
        $this->imageUrl = $imageUrl;
        $this->ecoTax = $ecoTax;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * @return CombinationAttributeInformation[]
     */
    public function getAttributesInformation(): array
    {
        return $this->attributesInformation;
    }

    /**
     * @return string
     */
    public function getCombinationName(): string
    {
        return $this->combinationName;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @return DecimalNumber
     */
    public function getImpactOnPrice(): DecimalNumber
    {
        return $this->impactOnPrice;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return DecimalNumber
     */
    public function getEcoTax(): DecimalNumber
    {
        return $this->ecoTax;
    }
}
