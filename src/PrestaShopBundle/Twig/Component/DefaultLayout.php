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

namespace PrestaShopBundle\Twig\Component;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Shop;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tools;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/default_layout.html.twig')]
class DefaultLayout
{

    public bool $display_header;

    public function __construct(
        private readonly LegacyContext             $context,
        private readonly bool                      $debugMode,
        private readonly RequestStack              $requestStack,
        private readonly Configuration             $configuration,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly UserProvider              $userProvider,
        private readonly TabRepository             $tabRepository,
        private readonly string                    $psVersion,
    )
    {
    }

    public function getIsoUser(): string
    {
        return $this->context->getLanguage()->getIsoCode();
    }

    public function getLangIsRtl(): bool
    {
        return (bool) $this->context->getLanguage()->isRTL();
    }

    public function getControllerName(): string
    {
        return htmlentities(Tools::getValue('controller'));
    }

    public function isCollapseMenu(): bool
    {
        $cookie = $this->context->getContext()->cookie;

        if (isset($cookie->collapse_menu)) {
            return boolval($cookie->collapse_menu);
        }

        return false;
    }

    public function isMultiShop(): bool
    {
        return Shop::isFeatureActive();
    }

    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    public function isMaintenanceMode(): bool
    {
        return !(bool) $this->configuration->get('PS_SHOP_ENABLE');
    }

    public function isMaintenanceAllowAdmins(): bool
    {
        return (bool) $this->configuration->get('PS_MAINTENANCE_ALLOW_ADMINS');
    }

    public function getJsRouterMetadata(): array
    {
        return [
            // base url for javascript router
            'base_url' => $this->requestStack->getCurrentRequest()->getBaseUrl(),
            //security token for javascript router
            'token' => $this->tokenManager->getToken($this->userProvider->getUsername())->getValue(),
        ];
    }

    public function getPsVersion(): string
    {
        return $this->psVersion;
    }

    public function getDefaultTabLink(): string
    {
        /** @var Tab $tab */
        $tab = $this->tabRepository->findOneBy(['id' => (int)$this->context->getContext()->employee->default_tab]);
        return $this->context->getLegacyAdminLink($tab->getClassName());
    }

    public function isShowNewOrders(): bool
    {
       return (bool)$this->configuration->get('PS_SHOW_NEW_ORDERS');
    }

    public function isShowNewCustomers(): bool
    {
        return (bool)$this->configuration->get('PS_SHOW_NEW_CUSTOMERS');
    }

    public function isShowNewMessages(): bool
    {
        return (bool)$this->configuration->get('PS_SHOW_NEW_MESSAGES');
    }
}
