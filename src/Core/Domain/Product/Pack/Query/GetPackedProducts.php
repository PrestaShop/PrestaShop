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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Retrieves product from a pack
 */
class GetPackedProducts
{
    /**
     * @var PackId
     */
    private $packId;

    /**
     * @var LanguageId
     */
    protected $languageId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    public function __construct(int $packId, int $languageId, ShopConstraint $shopConstraint)
    {
        $this->assertShopConstraintIsSupported($shopConstraint);
        $this->packId = new PackId($packId);
        $this->languageId = new LanguageId($languageId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return PackId
     */
    public function getPackId(): PackId
    {
        return $this->packId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param ShopConstraint $shopConstraint
     */
    private function assertShopConstraintIsSupported(ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopId()) {
            return;
        }

        throw new InvalidShopConstraintException('Only single shop constraint is supported');
    }
}
