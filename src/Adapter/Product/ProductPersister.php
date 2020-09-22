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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

class ProductPersister extends AbstractObjectModelPersister
{
    /**
     * @var ProductValidator
     */
    private $productValidator;

    /**
     * @param ProductValidator $productValidator
     */
    public function __construct(ProductValidator $productValidator)
    {
        $this->productValidator = $productValidator;
    }

    //@todo: implement add() in another pr

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function update(Product $product, array $propertiesToUpdate, int $errorCode): void
    {
        $this->fillProperties($product, $propertiesToUpdate);
        $this->productValidator->validate($product);
        $this->updateObjectModel($product, CannotUpdateProductException::class, $errorCode);
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     */
    private function fillProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillCustomizabilityProperties($product, $propertiesToUpdate);
        $this->fillSupplierProperties($product, $propertiesToUpdate);
    }

    /**
     * @param Product $product
     * @param array<string, mixed> $propertiesToUpdate
     */
    private function fillCustomizabilityProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillProperty($product, 'customizable', $propertiesToUpdate);
        $this->fillProperty($product, 'text_fields', $propertiesToUpdate);
        $this->fillProperty($product, 'uploadable_files', $propertiesToUpdate);
    }

    /**
     * @param Product $product
     * @param array<string, mixed> $propertiesToUpdate
     */
    private function fillSupplierProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillProperty($product, 'supplier_reference', $propertiesToUpdate);
        $this->fillProperty($product, 'id_supplier', $propertiesToUpdate);
        $this->fillProperty($product, 'wholesale_price', $propertiesToUpdate);
    }
}
