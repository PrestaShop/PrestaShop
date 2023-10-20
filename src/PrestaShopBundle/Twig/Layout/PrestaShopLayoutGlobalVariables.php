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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Shop;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tools;

class PrestaShopLayoutGlobalVariables
{
    private ?string $displayBackOfficeTop = null;

    public function __construct(
        private readonly LegacyContext $context,
        private readonly bool $debugMode,
        private readonly RequestStack $requestStack,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly UserProvider $userProvider,
        private readonly string $psVersion,
        private readonly Configuration $configuration,
        private readonly HookDispatcherInterface $hookDispatcher,
        private readonly MenuBuilder $menuBuilder,
        private readonly TabRepository $tabRepository,
        private readonly LocaleRepository $localeRepository,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    /**
     * Used to set the current locale in the context.
     * TODO: Need to be removed when we rework on contexts.
     */
    public function setCurrentLocale(): void
    {
        $this->context->getContext()->currentLocale = $this->localeRepository->getLocale(
            $this->context->getLanguage()->getLocale()
        );
    }

    /**
     * Enable New theme for smarty to avoid some problems with kpis for instance...
     * TODO: Need to be refactored, we need to find a proper way to initialize this smarty template directory when we display a migrated page
     */
    public function enableSmartyNewTheme(): void
    {
        $this->context->getContext()->smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'new-theme/template/');
    }

    public function getIsoUser(): string
    {
        return $this->context->getLanguage()->getIsoCode();
    }

    public function isSymfonyLayoutEnabled(): bool
    {
        return $this->featureFlagStateChecker->isEnabled('symfony_layout');
    }

    public function isRtlLanguage(): bool
    {
        return (bool) $this->context->getLanguage()->isRTL();
    }

    public function getControllerName(): string
    {
        return htmlentities(Tools::getValue('controller'));
    }

    public function isMultiShop(): bool
    {
        return Shop::isFeatureActive();
    }

    public function isMenuCollapsed(): bool
    {
        $cookie = $this->context->getContext()->cookie;

        if (isset($cookie->collapse_menu)) {
            return boolval($cookie->collapse_menu);
        }

        return false;
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

    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    public function installDirExists(): bool
    {
        return file_exists(_PS_ADMIN_DIR_ . '/../install');
    }

    public function getVersion(): string
    {
        return $this->psVersion;
    }

    public function getDefaultTabLink(): string
    {
        /** @var Tab $tab */
        $tab = $this->tabRepository->findOneBy(['id' => (int) $this->context->getContext()->employee->default_tab]);

        return $this->context->getLegacyAdminLink($tab->getClassName());
    }

    public function isMaintenanceEnabled(): bool
    {
        return !(bool) $this->configuration->get('PS_SHOP_ENABLE');
    }

    public function isFrontOfficeAccessibleForAdmins(): bool
    {
        return (bool) $this->configuration->get('PS_MAINTENANCE_ALLOW_ADMINS');
    }

    public function getDisplayBackOfficeTop(): ?string
    {
        if ($this->displayBackOfficeTop) {
            return $this->displayBackOfficeTop;
        }

        $renderedHook = $this->hookDispatcher->dispatchRenderingWithParameters('displayBackOfficeTop');

        if (!$content = $renderedHook->getContent()) {
            return null;
        }

        foreach ($content as $hookContent) {
            if (is_array($hookContent)) {
                $this->displayBackOfficeTop .= implode($hookContent);
            } else {
                $this->displayBackOfficeTop = $hookContent;
            }
        }

        return $this->displayBackOfficeTop;
    }

    public function isDisplayedWithTabs(): bool
    {
        return $this->menuBuilder->getCurrentTabLevel() >= 3;
    }

    public function getBaseUrl(): string
    {
        return $this->context->getContext()->shop->getBaseURL();
    }
}
