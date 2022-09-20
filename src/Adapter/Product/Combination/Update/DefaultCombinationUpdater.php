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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotAddCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Responsible for updating product default combination
 */
class DefaultCombinationUpdater
{
    /**
     * @var CombinationMultiShopRepository
     */
    private $combinationRepository;

    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @param CombinationMultiShopRepository $combinationRepository
     * @param ProductMultiShopRepository $productRepository
     */
    public function __construct(
        CombinationMultiShopRepository $combinationRepository,
        ProductMultiShopRepository $productRepository
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Marks the provided combination as default (combination->default_on)
     * and removes the mark from previous default combination
     *
     * Notice: Product->cache_default_attribute is updated in Product add(), update(), delete() methods.
     *
     * @see Product::updateDefaultAttribute()
     *
     * @param CombinationId $defaultCombinationId
     * @param ShopConstraint $shopConstraint
     */
    public function setDefaultCombination(CombinationId $defaultCombinationId, ShopConstraint $shopConstraint): void
    {
        //@todo; duplicated within combination repo. See if can be reused
        $newDefaultCombination = $this->combinationRepository->getByShopConstraint($defaultCombinationId, $shopConstraint);
        $productId = new ProductId((int) $newDefaultCombination->id_product);
        //@todo: im don't think this service is needed anymore,
        //       unless we leave space to handle product.cache_product_attribute later (cuz now it is already handled in object model in most cases)?
        $this->combinationRepository->setDefaultCombination(
            $defaultCombinationId,
            $shopConstraint
        );

        $this->productRepository->updateCachedDefaultCombination($productId);
    }
}
