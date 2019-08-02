<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Product\CustomizationField\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\CustomizationField\ValueObject\CustomizationFieldInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Update product customization fields.
 */
class UpdateProductCustomizationFieldsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CustomizationFieldInterface[]
     */
    private $customizationFields;

    /**
     *
     * @param int $productId
     * @param CustomizationFieldInterface[] $customizationFields
     */
    public function __construct(int $productId, array $customizationFields)
    {
        $this->productId = new ProductId($productId);
        $this->customizationFields = $customizationFields;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CustomizationFieldInterface[]
     */
    public function getCustomizationFields(): array
    {
        return $this->customizationFields;
    }
}
