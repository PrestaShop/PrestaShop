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
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class is responsible for preparing multistore elements that will be displayed in the BO
 * It does not control or render a BO page, the items being output are used inside other BO pages.
 */
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
     * This methods returns a Response object containing the multistore header displayed at the top of migrated pages
     *
     * @param bool $lockedToAllShopContext
     *
     * @return Response
     */
    public function header(bool $lockedToAllShopContext): Response
    {
        if (!$this->multistoreFeature->isUsed()) {
            return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
                'isMultistoreUsed' => false,
            ]);
        }

        $isAllShopContext = $this->multistoreContext->isAllShopContext();
        $isShopContext = $this->multistoreContext->isShopContext();

        if ($isShopContext) {
            $currentContext = $this->entityManager->getRepository(Shop::class)->findOneBy(['id' => $this->multistoreContext->getContextShopID()]);
        } elseif (!$isAllShopContext) {
            $shopGroupLegacy = $this->multistoreContext->getContextShopGroup();
            $currentContext = $this->entityManager->getRepository(ShopGroup::class)->findOneBy(['id' => $shopGroupLegacy->id]);
        } else {
            // use ShopGroup object as a the container for "all shops" context so that it can be used transparently in twig
            $currentContext = new ShopGroup();
            $currentContext->setName($this->trans('All shops', 'Admin.Global'));
            $currentContext->setColor('');
        }

        $groupList = [];
        if (!$lockedToAllShopContext) {
            $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);
        }

        $colorBrightnessCalculator = $this->get('prestashop.core.util.color_brightness_calculator');

        return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
            'isMultistoreUsed' => $this->multistoreFeature->isUsed(),
            'currentContext' => $currentContext,
            'groupList' => $groupList,
            'isShopContext' => $isShopContext,
            'link' => $this->getContext()->link,
            'isTitleDark' => empty($currentContext->getColor()) ? true : $colorBrightnessCalculator->isBright($currentContext->getColor()),
            'isAllShopContext' => $isAllShopContext,
            'isGroupContext' => $this->multistoreContext->isGroupShopContext(),
            'lockedToAllShopContext' => $lockedToAllShopContext,
        ]);
    }

    /**
     * @param ShopConfigurationInterface $configuration
     * @param string $configurationKey
     * @param int|null $groupId
     *
     * @return Response
     */
    public function configurationDropdown(ShopConfigurationInterface $configuration, string $configurationKey, int $groupId = null): Response
    {
        $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);
        $shopCustomizationChecker = $this->get('prestashop.multistore.customized_configuration_checker');
        $shouldDisplayDropdown = false;

        foreach ($groupList as $key => $group) {
            if (count($group->getShops()) === 0) {
                unset($groupList[$key]);
            }
            foreach ($group->getShops() as $shop) {
                if ($shopCustomizationChecker->isConfigurationCustomizedForThisShop($configurationKey, $shop) && $group->getId() === $groupId) {
                    $shouldDisplayDropdown = true;
                    break;
                }
            }
        }

        if (!$shouldDisplayDropdown) {
            // no dropdown is displayed if no shop overrides this configuration value, so we return an empty response.
            return new Response();
        }

        return $this->render('@PrestaShop/Admin/Multistore/dropdown.html.twig', [
            'groupList' => $groupList,
            'shopCustomizationChecker' => $shopCustomizationChecker,
            'configurationKey' => $configurationKey,
        ]);
    }
}
