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

namespace PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject;

use PrestaShopBundle\Entity\Shop;
use PrestaShop\PrestaShop\Adapter\Shop\Url\BaseUrlProvider;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlDataProvider;

class MultistoreHeaderShop
{
    /**
     * @var string
     */
    private $shopName;

    /**
     * @var string|null
     */
    private $shopColor;

    /**
     * @var string
     */
    private $shopUrlBO;

    /**
     * @var string
     */
    private $shopUrlFO;

    /**
     * MultistoreHeaderShop constructor.
     * @param Shop $shop
     * @param BaseUrlProvider $baseUrlProvider
     */
    public function __construct(Shop $shop)
    {
        $this->shopName = $shop->getName();
        $this->shopColor = $shop->getColor();

        $shopUrlData = new ShopUrlDataProvider($shop->getId());
    }

    public function getName()
    {
        return $this->shopName;
    }

    public function getColor()
    {
        return $this->color;
    }
}
