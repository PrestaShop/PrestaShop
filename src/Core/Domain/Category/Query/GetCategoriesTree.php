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

namespace PrestaShop\PrestaShop\Core\Domain\Category\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Provides Category tree list where each category holds its child categories
 */
final class GetCategoriesTree
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var ProductId|null
     */
    private $associatedProductId;

    /**
     * @param int $languageId
     * @param int|null $associatedProductId provide this to mark categories that are associated with specified product
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        int $languageId,
        ?int $associatedProductId
    ) {
        $this->languageId = new LanguageId($languageId);
        $this->associatedProductId = $associatedProductId ? new ProductId($associatedProductId) : null;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    /**
     * @return ProductId|null
     */
    public function getAssociatedProductId(): ?ProductId
    {
        return $this->associatedProductId;
    }
}
