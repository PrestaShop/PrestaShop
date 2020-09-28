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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Product\ProductValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

/**
 * Methods to access data storage for Product
 */
class ProductRepository extends AbstractObjectModelRepository
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

    /**
     * @param ProductId $productId
     *
     * @return Product
     *
     * @throws CoreException
     */
    public function get(ProductId $productId): Product
    {
        /** @var Product $product */
        $product = $this->getObjectModel(
            $productId->getValue(),
            Product::class,
            ProductNotFoundException::class
        );

        return $product;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function partialUpdate(Product $product, array $propertiesToUpdate, int $errorCode): void
    {
        $this->productValidator->validate($product);
        $this->partiallyUpdateObjectModel(
            $product,
            $propertiesToUpdate,
            CannotUpdateProductException::class,
            $errorCode
        );
    }
}
