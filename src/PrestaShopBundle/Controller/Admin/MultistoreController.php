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

namespace PrestaShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Component\HttpFoundation\Response;

class MultistoreController extends FrameworkBundleAdminController
{
    /**
     * @var MultistoreFeature
     */
    public $multistoreFeature;

    /**
     * @var Context
     */
    public $multistoreContext;

    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * @return Response
     */
    public function header(): Response
    {
        if (!$this->multistoreFeature->isUsed() || $this->multistoreContext->isAllShopContext()) {
            return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
                'isMultistoreUsed' => false,
            ]);
        }

        // $legacyUrlProvider = $this->get('prestashop.adapter.shop.url.base_url_provider');
        $shopGroupLegacy = $this->multistoreContext->getContextShopGroup();
        $shopGroup = $this->entityManager->getRepository(ShopGroup::class)->findOneById($shopGroupLegacy->id);
        $shop = null;
        if ($this->multistoreContext->isShopContext()) {
            $shop = $this->entityManager->getRepository(Shop::class)->findOneById($this->multistoreContext->getContextShopID());
        }

        $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);

        return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
            'isMultistoreUsed' => $this->multistoreFeature->isUsed(),
            'currentShop' => $shop ?: null,
            'currentShopGroup' => $shopGroup ?: null,
            'groupList' => $groupList ?: null,
        ]);
    }
}
