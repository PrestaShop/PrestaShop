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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates Product SEO options
 */
class UpdateProductSeoCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var string[]|null
     */
    private $localizedMetaTitles;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]|null
     */
    private $localizedLinkRewrites;

    /**
     * @var RedirectOption|null
     */
    private $redirectOption;

    /**
     * @param int $productId
     */
    public function __construct(int $productId, ShopConstraint $shopConstraint)
    {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles key => value pairs where each key represents language id
     *
     * @return UpdateProductSeoCommand
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): UpdateProductSeoCommand
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions key => value pairs where each key represents language id
     *
     * @return UpdateProductSeoCommand
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): UpdateProductSeoCommand
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedLinkRewrites(): ?array
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites key => value pairs where each key represents language id
     *
     * @return UpdateProductSeoCommand
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites): UpdateProductSeoCommand
    {
        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return RedirectOption|null
     */
    public function getRedirectOption(): ?RedirectOption
    {
        return $this->redirectOption;
    }

    /**
     * @param string $redirectType
     * @param int $redirectTarget
     *
     * @return UpdateProductSeoCommand
     */
    public function setRedirectOption(string $redirectType, int $redirectTarget): UpdateProductSeoCommand
    {
        $this->redirectOption = new RedirectOption($redirectType, $redirectTarget);

        return $this;
    }
}
