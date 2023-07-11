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

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Shop;

class ShopContextBuilder
{
    private ?int $shopContext = null;
    private ?int $shopGroupId = null;
    private ?int $shopId = null;

    public function __construct(
        private readonly ShopRepository $shopRepository
    ) {
    }

    public function build(): ShopContext
    {
        if (null === $this->shopContext) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build shop context as no shopContext has been define you need to call %s::setShopContext to define it before building the shop context',
                self::class
            ));
        }

        if (null === $this->shopId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build shop context as no shopId has been define you need to call %s::setShopId to define it before building the shop context',
                self::class
            ));
        }

        if ($this->shopContext === Shop::CONTEXT_SHOP) {
            $shopConstraint = ShopConstraint::shop($this->shopId);
        } elseif ($this->shopContext === Shop::CONTEXT_GROUP) {
            $shopConstraint = ShopConstraint::shopGroup($this->shopGroupId);
        } else {
            $shopConstraint = ShopConstraint::allShops();
        }

        return new ShopContext(
            $shopConstraint,
            $this->shopRepository->get(new ShopId($this->shopId))
        );
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function setAllShopsContext(): self
    {
        $this->shopContext = Shop::CONTEXT_ALL;

        return $this;
    }

    public function setShopContext(int $shopId): self
    {
        $this->shopId = $shopId;
        $this->shopContext = Shop::CONTEXT_SHOP;

        return $this;
    }

    public function setShopGroupContext(int $shopGroupId): self
    {
        $this->shopContext = Shop::CONTEXT_GROUP;
        $this->shopGroupId = $shopGroupId;

        return $this;
    }
}
