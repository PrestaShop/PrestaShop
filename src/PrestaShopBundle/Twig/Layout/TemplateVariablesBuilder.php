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

namespace PrestaShopBundle\Twig\Layout;

use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Allows you to construct variables used in rendering
 */
class TemplateVariablesBuilder
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly bool $debugMode,
        private readonly UserTokenManager $userTokenManager,
        private readonly UserProvider $userProvider,
        private readonly string $psVersion,
        private readonly ConfigurationInterface $configuration,
        private readonly MenuBuilder $menuBuilder,
        private readonly TabRepository $tabRepository,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly EmployeeContext $employeeContext,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
        private readonly LegacyControllerContext $legacyControllerContext,
        private readonly MultistoreFeature $multistoreFeature,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function build(): TemplateVariables
    {
        return new TemplateVariables(
            $this->languageContext->getIsoCode(),
            $this->featureFlagStateChecker->isEnabled('symfony_layout'),
            $this->languageContext->isRTL(),
            $this->legacyControllerContext->controller_name,
            $this->multistoreFeature->isActive(),
            $this->isMenuCollapsed(),
            $this->getJsRouterMetadata(),
            $this->debugMode,
            $this->installDirExists(),
            $this->psVersion,
            $this->getDefaultTabLink(),
            $this->isMaintenanceEnabled(),
            $this->isFrontOfficeAccessibleForAdmins(),
            $this->isDisplayedWithTabs(),
            $this->getBaseUrl(),
        );
    }

    private function isMenuCollapsed(): bool
    {
        $cookie = $this->context->getContext()->cookie;

        if (isset($cookie->collapse_menu)) {
            return boolval($cookie->collapse_menu);
        }

        return false;
    }

    private function getJsRouterMetadata(): array
    {
        return [
            // base url for javascript router
            'base_url' => $this->requestStack->getCurrentRequest()->getBaseUrl(),
            //security token for javascript router
            'token' => $this->userTokenManager->getSymfonyToken(),
        ];
    }

    private function installDirExists(): bool
    {
        return file_exists(_PS_ADMIN_DIR_ . '/../install');
    }

    private function getDefaultTabLink(): ?string
    {
        if ($this->employeeContext->getEmployee()) {
            /** @var Tab $tab */
            $tab = $this->tabRepository->findOneBy(['id' => $this->employeeContext->getEmployee()->getDefaultTabId()]);

            return $this->context->getLegacyAdminLink($tab->getClassName());
        }

        return null;
    }

    private function isMaintenanceEnabled(): bool
    {
        return !(bool) $this->configuration->get('PS_SHOP_ENABLE');
    }

    private function isFrontOfficeAccessibleForAdmins(): bool
    {
        return (bool) $this->configuration->get('PS_MAINTENANCE_ALLOW_ADMINS');
    }

    private function isDisplayedWithTabs(): bool
    {
        return $this->menuBuilder->getCurrentTabLevel() >= 3;
    }

    private function getBaseUrl(): string
    {
        return $this->shopContext->getBaseURL();
    }
}
