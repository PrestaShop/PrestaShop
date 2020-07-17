<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Product information for editing
 */
class ProductForEditing
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var ProductBasicInformation
     */
    private $basicInformation;

    /**
     * @var ProductCategoriesInformation
     */
    private $categoriesInformation;

    /**
     * @var ProductPricesInformation
     */
    private $pricesInformation;

    /**
     * @var ProductOptions
     */
    private $options;

    /**
     * @var ProductCustomizationOptions
     */
    private $customizationOptions;

    /**
     * @param int $productId
     * @param bool $active
     * @param ProductCustomizationOptions $customizationOptions
     * @param ProductBasicInformation $basicInformation
     * @param ProductCategoriesInformation $categoriesInformation
     * @param ProductPricesInformation $pricesInformation
     * @param ProductOptions $options
     */
    public function __construct(
        int $productId,
        bool $active,
        ProductCustomizationOptions $customizationOptions,
        ProductBasicInformation $basicInformation,
        ProductCategoriesInformation $categoriesInformation,
        ProductPricesInformation $pricesInformation,
        ProductOptions $options
    ) {
        $this->productId = $productId;
        $this->active = $active;
        $this->customizationOptions = $customizationOptions;
        $this->basicInformation = $basicInformation;
        $this->categoriesInformation = $categoriesInformation;
        $this->pricesInformation = $pricesInformation;
        $this->options = $options;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return ProductCustomizationOptions
     */
    public function getCustomizationOptions(): ProductCustomizationOptions
    {
        return $this->customizationOptions;
    }

    /**
     * @return ProductBasicInformation
     */
    public function getBasicInformation(): ProductBasicInformation
    {
        return $this->basicInformation;
    }

    /**
     * @return ProductCategoriesInformation
     */
    public function getCategoriesInformation(): ProductCategoriesInformation
    {
        return $this->categoriesInformation;
    }

    /**
     * @return ProductPricesInformation
     */
    public function getPricesInformation(): ProductPricesInformation
    {
        return $this->pricesInformation;
    }

    /**
     * @return ProductOptions
     */
    public function getOptions(): ProductOptions
    {
        return $this->options;
    }
}
